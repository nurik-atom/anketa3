<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\GallupReport;
use App\Models\GallupReportSheet;
use App\Models\GallupReportSheetIndex;
use App\Models\GallupReportSheetValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\Storage;
use Google\Client;
use Google\Service\Sheets;
use Google\Service\Docs;

class GallupController extends Controller
{
    public function parseGallupFromCandidateFile_404(Candidate $candidate)
    {
        // 1. Проверка наличия PDF-файла
        if (!$candidate->gallup_pdf || !Storage::disk('public')->exists($candidate->gallup_pdf)) {
            return response()->json(['error' => 'Gallup PDF файл не найден.'], 404);
        }

        if (!$this->isGallupPdf($candidate->gallup_pdf)) {
            return response()->json(['error' => 'Файл не является корректным Gallup PDF.'], 422);
        }

        $fullPath = storage_path('app/public/' . $candidate->gallup_pdf);

        // 2. Чтение первой страницы PDF
        $parser = new \Smalot\PdfParser\Parser();
        $pdf = $parser->parseFile($fullPath);
        $pages = $pdf->getPages();

        if (empty($pages)) {
            return response()->json(['error' => 'PDF не содержит страниц.'], 422);
        }

        $firstPageText = $pages[0]->getText();

        // 3. Извлечение талантов строго с номерами от 1 до 34
        preg_match_all('/\b([1-9]|[1-2][0-9]|3[0-4])\.\s+([A-Za-z-]+)/', $firstPageText, $matches);

        $numbers = $matches[1] ?? [];
        $talents = $matches[2] ?? [];

        // 4. Проверка, что извлечены строго 34 таланта с номерами от 1 до 34
        if (count($talents) !== 34 || max($numbers) != 34 || min($numbers) != 1) {
            return response()->json([
                'error' => 'Найдено ' . count($talents) . ' талантов. Ожидается 34.',
                'debug' => $talents,
            ], 422);
        }

        // 5. Удаление старых и сохранение новых талантов
        $candidate->gallupTalents()->delete();

        foreach ($talents as $index => $name) {
            $candidate->gallupTalents()->create([
                'name' => trim($name),
                'position' => $index + 1,
            ]);
        }
        // Обновление Google Sheets
        $this->updateGoogleSheet($candidate, $talents);

        // Скачивание PDF листа Main (Russian)
        $this->downloadSheetPdf(
            '1k8RZfrwWyivGJqLZ9IXYsBi55tyX4oGZDsVtTWRR1Xw', // Spreadsheet ID
            '1270262254', // GID листа Main (Russian)
            storage_path("app/public/gallup_pdf_result_{$candidate->id}.pdf")
        );

        return response()->json([
            'message' => 'Gallup таланты успешно обновлены.',
            'talents' => array_map(fn($name, $i) => [
                'position' => $i + 1,
                'name' => $name,
            ], $talents, array_keys($talents))
        ]);
    }
    public function isGallupPdf(string $relativePath):bool
    {
        if (!Storage::disk('public')->exists($relativePath)) {
            return false;
        }

        $fullPath = storage_path('app/public/' . $relativePath);

        try {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($fullPath);
            $text = $pdf->getText();
            $pages = $pdf->getPages();

            // Ключевые признаки Gallup-отчета
//            $hasCorrectPageCount = count($pages) === 26;
            $containsCliftonHeader = str_contains($text, 'Gallup, Inc. All rights reserved.');
            $containsTalentList = preg_match('/1\.\s+[A-Za-z-]+/', $text);
            $containsTalentList34 = preg_match('/34\.\s+[A-Za-z-]+/', $text);

            return $containsCliftonHeader && $containsTalentList && $containsTalentList34;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function parseGallupFromCandidateFile(Candidate $candidate)
    {
        if (!$candidate->gallup_pdf || !Storage::disk('public')->exists($candidate->gallup_pdf)) {
            return response()->json(['error' => 'Файл не найден.'], 404);
        }

        if (!$this->isGallupPdf($candidate->gallup_pdf)) {
            return response()->json(['error' => 'Файл не является корректным Gallup PDF.'], 422);
        }

        $fullPath = storage_path('app/public/' . $candidate->gallup_pdf);

        $parser = new Parser();
        $pdf = $parser->parseFile($fullPath);
        $pages = $pdf->getPages();

        if (empty($pages)) {
            return response()->json(['error' => 'PDF не содержит страниц.'], 422);
        }

        $firstPageText = $pages[0]->getText();

        preg_match_all('/\b([1-9]|[1-2][0-9]|3[0-4])\.\s+([A-Za-z-]+)/', $firstPageText, $matches);
        $numbers = $matches[1] ?? [];
        $talents = $matches[2] ?? [];

        if (count($talents) !== 34 || max($numbers) != 34 || min($numbers) != 1) {
            return response()->json([
                'error' => 'Найдено ' . count($talents) . ' талантов. Ожидается 34.',
                'debug' => $talents,
            ], 422);
        }

        $candidate->gallupTalents()->delete();

        foreach ($talents as $index => $name) {
            $candidate->gallupTalents()->create([
                'name' => trim($name),
                'position' => $index + 1,
            ]);
        }

        // Получаем все активные листы отчетов из базы данных
        $reportSheets = GallupReportSheet::with('indices')->get();

        foreach ($reportSheets as $reportSheet) {
            // Обновление Google Sheets
            $this->updateGoogleSheetByCellMap($candidate, $talents, $reportSheet);

            Log::info('Перед вызовом importFormulaValues', [
                'reportSheet_id' => $reportSheet->id,
                'candidate_id' => $candidate->id,
            ]);

            $this->importFormulaValues($reportSheet, $candidate);
            // Скачивание PDF листа
            $this->downloadSheetPdf(
                $candidate,
                $reportSheet->spreadsheet_id,
                $reportSheet->gid,
                $reportSheet->name_report
            );


        }

        return response()->json([
            'message' => 'Данные Gallup обновлены, Google Sheet заполнен, PDF сохранён.',
        ]);
    }

    protected function updateGoogleSheetByCellMap(Candidate $candidate, array $talents, GallupReportSheet $reportSheet)
    {
        $client = new \Google\Client();
        $client->setAuthConfig(storage_path('app/google/credentials.json'));
        $client->addScope(\Google\Service\Sheets::SPREADSHEETS);
        $client->useApplicationDefaultCredentials();
        $spreadsheetId = $reportSheet->spreadsheet_id;
        $sheets = new \Google\Service\Sheets($client);
//        $spreadsheetId = '1k8RZfrwWyivGJqLZ9IXYsBi55tyX4oGZDsVtTWRR1Xw';

        $talentOrder = [
            'Achiever', 'Discipline', 'Arranger', 'Focus', 'Belief', 'Responsibility',
            'Consistency', 'Restorative', 'Deliberative', 'Activator', 'Maximizer',
            'Command', 'Self-Assurance', 'Communication', 'Significance', 'Competition', 'Woo',
            'Adaptability', 'Includer', 'Connectedness', 'Individualization', 'Developer',
            'Positivity', 'Empathy', 'Relator', 'Harmony', 'Analytical', 'Input',
            'Context', 'Intellection', 'Futuristic', 'Learner', 'Ideation', 'Strategic'
        ];

        // Создаём строку B3:AJ3 = [Имя, 34 позиции]
        $row = [$candidate->full_name];

        foreach ($talentOrder as $talentName) {
            $index = array_search($talentName, $talents);
            $row[] = $index !== false ? $index + 1 : '';
        }

        // Ограничим на всякий случай длину в 35
        $row = array_slice($row, 0, 35);

        $body = new \Google\Service\Sheets\ValueRange([
            'range' => 'Info!B3:AJ3',
            'values' => [$row]
        ]);

        $sheets->spreadsheets_values->update($spreadsheetId, 'Info!B3:AJ3', $body, [
            'valueInputOption' => 'RAW'
        ]);
    }

    protected function downloadSheetPdf(Candidate $candidate, string $spreadsheetId, string $gid, string $reportType)
    {
        // Подготовка Google клиента
        $client = new \Google\Client();
        $client->setAuthConfig(storage_path('app/google/credentials.json'));
        $client->addScope('https://www.googleapis.com/auth/drive.readonly');
        $client->useApplicationDefaultCredentials();

        $accessToken = $client->fetchAccessTokenWithAssertion()['access_token'];

        // Формируем URL экспорта
        $url = "https://docs.google.com/spreadsheets/d/{$spreadsheetId}/export?" . http_build_query([
                'format' => 'pdf',
                'gid' => $gid,
                'portrait' => 'true',
                'size' => 'A4',
                'fitw' => 'true',
                'sheetnames' => 'false',
                'printtitle' => 'false',
                'pagenum' => 'false',
                'gridlines' => 'false',
                'fzr' => 'false'
            ]);

        // Загружаем PDF
        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'Authorization' => "Bearer $accessToken"
        ])->get($url);

        if (!$response->successful()) {
            throw new \Exception("Ошибка при скачивании PDF: " . $response->status());
        }

        // Удаляем старый отчёт, если есть
        $existing = GallupReport::where('candidate_id', $candidate->id)
            ->where('type', $reportType)
            ->first();

        if ($existing) {
            Storage::disk('public')->delete($existing->pdf_file);
            $existing->delete();
        }

        // Подготовка пути для хранения
        $folder = 'reports/' . str_replace(' ', '_', $candidate->full_name) . '_' . $candidate->id;
        $fileName = str_replace(' ', '_', $candidate->full_name) . "_{$reportType}.pdf";
        $fullPath = "{$folder}/{$fileName}";

        // Сохраняем в storage/app/public/...
        Storage::disk('public')->put($fullPath, $response->body());

        // Записываем в таблицу gallup_reports
        GallupReport::create([
            'candidate_id' => $candidate->id,
            'type' => $reportType,
            'pdf_file' => $fullPath,
        ]);
    }

    public function importFormulaValues_old(GallupReportSheet $reportSheet, Candidate $candidate)
    {
        Log::info('Импорт психотипов начат', ['report_id' => $candidate->id]);
        $spreadsheetId = $reportSheet->spreadsheet_id;
        $sheetName = 'Formula';

        // Авторизация Google
        $client = new \Google\Client();
        $client->setAuthConfig(storage_path('app/google/credentials.json'));
        $client->addScope(Sheets::SPREADSHEETS_READONLY);
        $client->useApplicationDefaultCredentials();

        $service = new Sheets($client);

        // Удаляем старые значения (если нужно)
        GallupReportSheetValue::where('gallup_report_sheet_id', $reportSheet->id)->where('candidate_id', $candidate->id)->delete();

        // Получаем список нужных ячеек
        $indexes = GallupReportSheetIndex::where('gallup_report_sheet_id', $reportSheet->id)->get();
        Log::info('GallupReportSheetIndex values', ['indexes' => $indexes->toArray()]);
        foreach ($indexes as $index) {
            $range = "{$sheetName}!{$index->index}";
            $response = $service->spreadsheets_values->get($spreadsheetId, $range);
            $value = $response->getValues()[0][0] ?? null;

            if ($value === null) continue;

            GallupReportSheetValue::create([
                'gallup_report_sheet_id' => $reportSheet->id,
                'candidate_id' => $candidate->id,
                'user_id' => $candidate->user_id,
                'type' => $index->type,
                'name' => $index->name,
                'value' => $value,
            ]);

            Log::info('Импорт значения', [
                'index' => $index->index,
                'name' => $index->name,
                'value' => $value,
            ]);
        }

        Log::info('Импорт психотипов завершён');
    }

    public function importFormulaValues(GallupReportSheet $reportSheet, Candidate $candidate)
    {
        Log::info('Импорт психотипов начат', ['report_id' => $candidate->id]);
        $spreadsheetId = $reportSheet->spreadsheet_id;
        $sheetName = 'Formula';

        // Авторизация Google
        $client = new \Google\Client();
        $client->setAuthConfig(storage_path('app/google/credentials.json'));
        $client->addScope(\Google\Service\Sheets::SPREADSHEETS_READONLY);
        $client->useApplicationDefaultCredentials();

        $service = new Sheets($client);

        // Получаем список нужных ячеек
        $indexes = GallupReportSheetIndex::where('gallup_report_sheet_id', $reportSheet->id)->get();
        if ($indexes->isEmpty()) return;

        // Определяем минимальную и максимальную ячейку (например: O21:AJ24)
        $minRow = $maxRow = null;
        $minCol = $maxCol = null;

        $positions = [];

        foreach ($indexes as $index) {
            [$col, $row] = $this->cellToCoordinates($index->index);

            $minRow = is_null($minRow) ? $row : min($minRow, $row);
            $maxRow = is_null($maxRow) ? $row : max($maxRow, $row);
            $minCol = is_null($minCol) ? $col : min($minCol, $col);
            $maxCol = is_null($maxCol) ? $col : max($maxCol, $col);

            $positions[$index->index] = [
                'row' => $row,
                'col' => $col,
                'type' => $index->type,
                'name' => $index->name,
            ];
        }

        // Преобразуем координаты обратно в строку
        $minCell = $this->coordinatesToCell($minCol, $minRow);
        $maxCell = $this->coordinatesToCell($maxCol, $maxRow);
        $range = "{$sheetName}!{$minCell}:{$maxCell}";

        Log::info('Запрашиваем диапазон: ' . $range);

        $response = $service->spreadsheets_values->get($spreadsheetId, $range);
        $matrix = $response->getValues();

        // Удаляем старые значения
        GallupReportSheetValue::where('gallup_report_sheet_id', $reportSheet->id)
            ->where('candidate_id', $candidate->id)
            ->delete();

        foreach ($positions as $cell => $meta) {
            $relativeRow = $meta['row'] - $minRow;
            $relativeCol = $meta['col'] - $minCol;

            $value = $matrix[$relativeRow][$relativeCol] ?? null;
            if ($value === null) continue;

            if (is_string($value)) {
                $value = preg_replace('/\s*%$/', '', $value);
            }

            GallupReportSheetValue::create([
                'gallup_report_sheet_id' => $reportSheet->id,
                'candidate_id' => $candidate->id,
                'user_id' => $candidate->user_id,
                'type' => $meta['type'],
                'name' => $meta['name'],
                'value' => (int) $value,
            ]);

            Log::info('Импорт значения', [
                'cell' => $cell,
                'name' => $meta['name'],
                'value' => $value,
            ]);
        }

        Log::info('Импорт психотипов завершён');
    }

    protected function cellToCoordinates($cell)
    {
        preg_match('/^([A-Z]+)(\d+)$/', strtoupper($cell), $matches);
        $letters = $matches[1];
        $row = (int)$matches[2];

        $col = 0;
        foreach (str_split($letters) as $char) {
            $col = $col * 26 + (ord($char) - 64);
        }

        return [$col, $row];
    }

    protected function coordinatesToCell($col, $row)
    {
        $letters = '';
        while ($col > 0) {
            $col--;
            $letters = chr($col % 26 + 65) . $letters;
            $col = intdiv($col, 26);
        }

        return $letters . $row;
    }

    /**
     * Создаёт Google Docs с данными из нескольких Google Sheets
     */
    public function createGoogleDocsFromSheets(Candidate $candidate)
    {
        // Получаем все активные листы отчетов
        $reportSheets = GallupReportSheet::with('indices')->get();
        
        if ($reportSheets->isEmpty()) {
            return response()->json(['error' => 'Нет активных отчетов для обработки'], 422);
        }

        // Создаём новый Google Docs
        $documentId = $this->createGoogleDoc($candidate);
        
        // Собираем данные из всех Google Sheets
        $allData = [];
        foreach ($reportSheets as $reportSheet) {
            $sheetData = $this->getSheetData($reportSheet, $candidate);
            $allData[$reportSheet->name_report] = $sheetData;
        }
        
        // Заполняем Google Docs данными
        $this->populateGoogleDoc($documentId, $candidate, $allData);
        
        // Сохраняем ссылку на документ
        $this->saveDocumentLink($candidate, $documentId);
        
        return response()->json([
            'message' => 'Google Docs создан и заполнен данными',
            'document_id' => $documentId,
            'document_url' => "https://docs.google.com/document/d/{$documentId}"
        ]);
    }

    /**
     * Создаёт новый Google Docs документ
     */
    protected function createGoogleDoc(Candidate $candidate)
    {
        $client = new Client();
        $client->setAuthConfig(storage_path('app/google/credentials.json'));
        $client->addScope([
            \Google\Service\Docs::DOCUMENTS,
            \Google\Service\Drive::DRIVE_FILE
        ]);
        $client->useApplicationDefaultCredentials();

        $docsService = new \Google\Service\Docs($client);
        
        // Создаём новый документ
        $document = new \Google\Service\Docs\Document([
            'title' => "Отчет по кандидату: {$candidate->full_name}"
        ]);
        
        $response = $docsService->documents->create($document);
        
        return $response->getDocumentId();
    }

    /**
     * Получает данные из Google Sheets
     */
    protected function getSheetData(GallupReportSheet $reportSheet, Candidate $candidate)
    {
        $client = new Client();
        $client->setAuthConfig(storage_path('app/google/credentials.json'));
        $client->addScope(\Google\Service\Sheets::SPREADSHEETS_READONLY);
        $client->useApplicationDefaultCredentials();

        $sheetsService = new Sheets($client);
        
        // Получаем данные из листа Formula
        $range = 'Formula!A1:Z100'; // Adjust range as needed
        $response = $sheetsService->spreadsheets_values->get(
            $reportSheet->spreadsheet_id, 
            $range
        );
        
        return $response->getValues() ?? [];
    }

    /**
     * Заполняет Google Docs данными из нескольких листов
     */
    protected function populateGoogleDoc($documentId, Candidate $candidate, array $allData)
    {
        $client = new Client();
        $client->setAuthConfig(storage_path('app/google/credentials.json'));
        $client->addScope(\Google\Service\Docs::DOCUMENTS);
        $client->useApplicationDefaultCredentials();

        $docsService = new \Google\Service\Docs($client);
        
        // Формируем контент для документа
        $requests = [];
        
        // Заголовок документа
        $requests[] = new \Google\Service\Docs\Request([
            'insertText' => [
                'location' => ['index' => 1],
                'text' => "ОТЧЕТ ПО КАНДИДАТУ\n\n"
            ]
        ]);
        
        // Информация о кандидате
        $candidateInfo = "Имя: {$candidate->full_name}\n";
        $candidateInfo .= "Дата создания отчета: " . now()->format('d.m.Y H:i') . "\n\n";
        
        $requests[] = new \Google\Service\Docs\Request([
            'insertText' => [
                'location' => ['index' => 1],
                'text' => $candidateInfo
            ]
        ]);
        
        // Добавляем данные из каждого листа
        foreach ($allData as $sheetName => $sheetData) {
            $requests[] = new \Google\Service\Docs\Request([
                'insertText' => [
                    'location' => ['index' => 1],
                    'text' => "\n=== {$sheetName} ===\n\n"
                ]
            ]);
            
            // Конвертируем данные листа в текст
            $tableText = $this->convertSheetDataToText($sheetData);
            
            $requests[] = new \Google\Service\Docs\Request([
                'insertText' => [
                    'location' => ['index' => 1],
                    'text' => $tableText . "\n\n"
                ]
            ]);
        }
        
        // Выполняем все запросы
        $batchUpdateRequest = new \Google\Service\Docs\BatchUpdateDocumentRequest([
            'requests' => array_reverse($requests) // Reverse to maintain order
        ]);
        
        $docsService->documents->batchUpdate($documentId, $batchUpdateRequest);
    }

    /**
     * Конвертирует данные листа в текстовый формат
     */
    protected function convertSheetDataToText(array $sheetData)
    {
        $text = '';
        
        foreach ($sheetData as $row) {
            if (empty($row)) continue;
            
            $text .= implode(' | ', $row) . "\n";
        }
        
        return $text;
    }

    /**
     * Сохраняет ссылку на созданный документ
     */
    protected function saveDocumentLink(Candidate $candidate, $documentId)
    {
        // Можно сохранить в отдельную таблицу или в существующую
        GallupReport::create([
            'candidate_id' => $candidate->id,
            'type' => 'Google_Docs_Combined',
            'pdf_file' => null, // Или можно экспортировать в PDF
            'document_id' => $documentId,
            'document_url' => "https://docs.google.com/document/d/{$documentId}"
        ]);
    }

    /**
     * Экспортирует Google Docs в PDF
     */
    public function exportGoogleDocsToPdf(Candidate $candidate, $documentId)
    {
        $client = new Client();
        $client->setAuthConfig(storage_path('app/google/credentials.json'));
        $client->addScope(\Google\Service\Drive::DRIVE_READONLY);
        $client->useApplicationDefaultCredentials();

        $accessToken = $client->fetchAccessTokenWithAssertion()['access_token'];
        
        // URL для экспорта Google Docs в PDF
        $url = "https://docs.google.com/document/d/{$documentId}/export?format=pdf";
        
        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'Authorization' => "Bearer $accessToken"
        ])->get($url);
        
        if (!$response->successful()) {
            throw new \Exception("Ошибка при экспорте Google Docs в PDF: " . $response->status());
        }
        
        // Сохраняем PDF
        $folder = 'reports/' . str_replace(' ', '_', $candidate->full_name) . '_' . $candidate->id;
        $fileName = str_replace(' ', '_', $candidate->full_name) . "_Combined_Report.pdf";
        $fullPath = "{$folder}/{$fileName}";
        
        Storage::disk('public')->put($fullPath, $response->body());
        
        // Обновляем запись в базе данных
        GallupReport::where('candidate_id', $candidate->id)
            ->where('type', 'Google_Docs_Combined')
            ->update(['pdf_file' => $fullPath]);
        
        return $fullPath;
    }

    /**
     * Создаёт таблицу для данных листа
     */
    protected function createTableForSheetData(array $sheetData)
    {
        $rows = count($sheetData);
        $cols = 0;
        
        // Определяем максимальное количество колонок
        foreach ($sheetData as $row) {
            $cols = max($cols, count($row));
        }
        
        // Создаём таблицу
        $tableRequest = new \Google\Service\Docs\Request([
            'insertTable' => [
                'location' => ['index' => 1],
                'rows' => $rows,
                'columns' => $cols
            ]
        ]);
        
        return $tableRequest;
    }

    /**
     * Улучшенная версия создания Google Docs с таблицами
     */
    public function createGoogleDocsFromSheetsAdvanced(Candidate $candidate)
    {
        // Получаем все активные листы отчетов
        $reportSheets = GallupReportSheet::with('indices')->get();
        
        if ($reportSheets->isEmpty()) {
            return response()->json(['error' => 'Нет активных отчетов для обработки'], 422);
        }

        // Создаём новый Google Docs
        $documentId = $this->createGoogleDoc($candidate);
        
        // Собираем данные из всех Google Sheets
        $allData = [];
        foreach ($reportSheets as $reportSheet) {
            // Получаем специфичные данные для каждого листа
            $sheetData = $this->getSpecificSheetData($reportSheet, $candidate);
            $allData[$reportSheet->name_report] = $sheetData;
        }
        
        // Заполняем Google Docs данными с таблицами
        $this->populateGoogleDocAdvanced($documentId, $candidate, $allData);
        
        // Сохраняем ссылку на документ
        $this->saveDocumentLink($candidate, $documentId);
        
        return response()->json([
            'message' => 'Google Docs создан с таблицами и форматированием',
            'document_id' => $documentId,
            'document_url' => "https://docs.google.com/document/d/{$documentId}"
        ]);
    }

    /**
     * Получает специфичные данные для отчета из Google Sheets
     */
    protected function getSpecificSheetData(GallupReportSheet $reportSheet, Candidate $candidate)
    {
        $client = new Client();
        $client->setAuthConfig(storage_path('app/google/credentials.json'));
        $client->addScope(\Google\Service\Sheets::SPREADSHEETS_READONLY);
        $client->useApplicationDefaultCredentials();

        $sheetsService = new Sheets($client);
        
        // Получаем данные из листа Formula с конкретными значениями
        $values = GallupReportSheetValue::where('gallup_report_sheet_id', $reportSheet->id)
            ->where('candidate_id', $candidate->id)
            ->get();
        
        $sheetData = [];
        
        // Заголовок
        $sheetData[] = ['Показатель', 'Значение'];
        
        // Данные
        foreach ($values as $value) {
            $sheetData[] = [$value->name, $value->value . '%'];
        }
        
        return $sheetData;
    }

    /**
     * Заполняет Google Docs с расширенным форматированием
     */
    protected function populateGoogleDocAdvanced($documentId, Candidate $candidate, array $allData)
    {
        $client = new Client();
        $client->setAuthConfig(storage_path('app/google/credentials.json'));
        $client->addScope(\Google\Service\Docs::DOCUMENTS);
        $client->useApplicationDefaultCredentials();

        $docsService = new \Google\Service\Docs($client);
        
        // Заголовок документа
        $requests = [];
        
        $requests[] = new \Google\Service\Docs\Request([
            'insertText' => [
                'location' => ['index' => 1],
                'text' => "ОТЧЕТ ПО КАНДИДАТУ\n\n"
            ]
        ]);
        
        // Информация о кандидате
        $candidateInfo = "ФИО: {$candidate->full_name}\n";
        $candidateInfo .= "Дата создания отчета: " . now()->format('d.m.Y H:i') . "\n\n";
        
        $requests[] = new \Google\Service\Docs\Request([
            'insertText' => [
                'location' => ['index' => 1],
                'text' => $candidateInfo
            ]
        ]);
        
        // Добавляем данные из каждого листа
        foreach ($allData as $sheetName => $sheetData) {
            $requests[] = new \Google\Service\Docs\Request([
                'insertText' => [
                    'location' => ['index' => 1],
                    'text' => "\n" . strtoupper($sheetName) . "\n"
                ]
            ]);
            
            $requests[] = new \Google\Service\Docs\Request([
                'insertText' => [
                    'location' => ['index' => 1],
                    'text' => str_repeat('-', 50) . "\n\n"
                ]
            ]);
            
            // Добавляем таблицу с данными
            if (!empty($sheetData)) {
                $requests[] = new \Google\Service\Docs\Request([
                    'insertTable' => [
                        'location' => ['index' => 1],
                        'rows' => count($sheetData),
                        'columns' => count($sheetData[0])
                    ]
                ]);
            }
        }
        
        // Выполняем все запросы
        $batchUpdateRequest = new \Google\Service\Docs\BatchUpdateDocumentRequest([
            'requests' => array_reverse($requests)
        ]);
        
        $docsService->documents->batchUpdate($documentId, $batchUpdateRequest);
        
        // Заполняем таблицы данными
        $this->fillTablesWithData($docsService, $documentId, $allData);
    }

    /**
     * Заполняет таблицы данными после их создания
     */
    protected function fillTablesWithData($docsService, $documentId, array $allData)
    {
        // Получаем обновленный документ
        $document = $docsService->documents->get($documentId);
        
        $requests = [];
        $tableIndex = 0;
        
        foreach ($allData as $sheetName => $sheetData) {
            if (empty($sheetData)) continue;
            
            // Находим таблицу по индексу
            $tables = [];
            foreach ($document->getBody()->getContent() as $element) {
                if ($element->getTable()) {
                    $tables[] = $element->getTable();
                }
            }
            
            if (isset($tables[$tableIndex])) {
                $table = $tables[$tableIndex];
                
                // Заполняем ячейки таблицы
                foreach ($sheetData as $rowIndex => $rowData) {
                    foreach ($rowData as $colIndex => $cellData) {
                        $requests[] = new \Google\Service\Docs\Request([
                            'insertText' => [
                                'location' => [
                                    'index' => $table->getTableRows()[$rowIndex]->getTableCells()[$colIndex]->getContent()[0]->getStartIndex() + 1
                                ],
                                'text' => (string)$cellData
                            ]
                        ]);
                    }
                }
                
                $tableIndex++;
            }
        }
        
        if (!empty($requests)) {
            $batchUpdateRequest = new \Google\Service\Docs\BatchUpdateDocumentRequest([
                'requests' => $requests
            ]);
            
            $docsService->documents->batchUpdate($documentId, $batchUpdateRequest);
        }
    }
}
