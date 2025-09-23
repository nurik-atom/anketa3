<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\GallupParseHistory;
use App\Models\GallupReport;
use App\Models\GallupReportSheet;
use App\Models\GallupReportSheetIndex;
use App\Models\GallupReportSheetValue;
use Google\Client;
use Google\Service\Drive;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Knp\Snappy\Pdf;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;
use setasign\Fpdi\Fpdi;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\Storage;
use Google\Service\Sheets;
use Spatie\Browsershot\Browsershot;

class GallupController extends Controller
{
    /**
     * Записать шаг в историю
     */
    protected function logStep(Candidate $candidate, string $step, string $status = 'in_progress', ?string $details = null): void
    {
        // Обновляем поле в кандидате
        $candidate->update(['step_parse_gallup' => $step]);
        
        // Записываем в историю
        GallupParseHistory::createHistory(
            $candidate->id,
            $step,
            $status,
            $details
        );
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
        // Шаг 1: Проверка файла
        $this->logStep($candidate, 'Проверка файла');
        
        if (!$candidate->gallup_pdf || !Storage::disk('public')->exists($candidate->gallup_pdf)) {
            $this->logStep($candidate, 'Ошибка: Файл не найден', 'error', 'Файл Gallup PDF не найден в файловой системе');
            return response()->json(['error' => 'Файл не найден.'], 404);
        }

        if (!$this->isGallupPdf($candidate->gallup_pdf)) {
            $this->logStep($candidate, 'Ошибка: Неверный формат PDF', 'error', 'Файл не является корректным Gallup PDF');
            return response()->json(['error' => 'Файл не является корректным Gallup PDF.'], 422);
        }

        // Шаг 2: Парсинг PDF
        $this->logStep($candidate, 'Парсинг PDF');
        
        $fullPath = storage_path('app/public/' . $candidate->gallup_pdf);

        $parser = new Parser();
        $pdf = $parser->parseFile($fullPath);
        $pages = $pdf->getPages();

        if (empty($pages)) {
            $this->logStep($candidate, 'Ошибка: PDF не содержит страниц', 'error', 'PDF файл пустой или поврежден');
            return response()->json(['error' => 'PDF не содержит страниц.'], 422);
        }

        $firstPageText = $pages[0]->getText();

        preg_match_all('/\b([1-9]|[1-2][0-9]|3[0-4])\.\s+([A-Za-z-]+)/', $firstPageText, $matches);
        $numbers = $matches[1] ?? [];
        $talents = $matches[2] ?? [];

        if (count($talents) !== 34 || max($numbers) != 34 || min($numbers) != 1) {
            $this->logStep($candidate, 'Ошибка: Найдено ' . count($talents) . ' талантов вместо 34', 'error', 'Ожидается 34 таланта, найдено: ' . count($talents));
            return response()->json([
                'error' => 'Найдено ' . count($talents) . ' талантов. Ожидается 34.',
                'debug' => $talents,
            ], 422);
        }

        // Шаг 3: Обновление талантов
        $this->logStep($candidate, 'Обновление талантов');
        
        // Получаем текущие таланты из базы
        $existingTalents = $candidate->gallupTalents()
            ->orderBy('position')
            ->pluck('name')
            ->toArray();

        // Проверка на изменения
        $hasChanged = $existingTalents !== $talents;
        
        // //TODO: Убрать после тестирования
        // $hasChanged = true;
        //! Если изменения есть, то обновляем таланты
        if ($hasChanged) {
            $candidate->gallupTalents()->delete();

            foreach ($talents as $index => $name) {
                $candidate->gallupTalents()->create([
                    'name' => trim($name),
                    'position' => $index + 1,
                ]);
            }

            // Шаг 4: Обработка отчетов
            $this->logStep($candidate, 'Обработка отчетов');
            
            // Получаем все активные листы отчетов из базы данных
            $reportSheets = GallupReportSheet::with('indices')->orderBy('id', 'desc')->get();

            foreach ($reportSheets as $reportSheet) {
                // Шаг 5: Обновление Google Sheets
                $this->logStep($candidate, "Обновление Google Sheets: {$reportSheet->name_report}");
                $this->updateGoogleSheetByCellMap($candidate, $talents, $reportSheet);

                Log::info('Перед вызовом importFormulaValues', [
                    'reportSheet_id' => $reportSheet->id,
                    'candidate_id' => $candidate->id,
                ]);

                // Шаг 6: Импорт формул
                $this->logStep($candidate, "Импорт формул: {$reportSheet->name_report}");
                $this->importFormulaValues($reportSheet, $candidate);
                
                // Шаг 7: Скачивание PDF
                $this->logStep($candidate, "Скачивание PDF: {$reportSheet->name_report}");
                $this->downloadSheetPdf(
                    $candidate,
                    $reportSheet
                );
            }
            
            // Шаг 8: Завершение
            $this->logStep($candidate, 'Завершено успешно', 'completed', 'Все отчеты успешно обработаны');
        } else {
            $this->logStep($candidate, 'Изменений не обнаружено', 'completed', 'Таланты не изменились, обработка не требуется');
        }

        // Убираем объединение PDF из основного процесса - теперь генерируем по требованию
        // $mergedPath = $this->mergeCandidateReportPdfs($candidate);
        // $candidate->anketa_pdf = $mergedPath;
        // $candidate->save();

        return response()->json([
            'message' => 'Данные Gallup обновлены, Google Sheet заполнен.',
            'step' => $candidate->step_parse_gallup
        ]);
    }

    protected function updateGoogleSheetByCellMap(Candidate $candidate, array $talents, GallupReportSheet $reportSheet)
    {
        try {
            $client = new \Google\Client();
            $client->setAuthConfig(storage_path('app/google/credentials.json'));
            $client->addScope(\Google\Service\Sheets::SPREADSHEETS);
            $client->useApplicationDefaultCredentials();
            $spreadsheetId = $reportSheet->spreadsheet_id;
            $sheets = new \Google\Service\Sheets($client);

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
        } catch (\Exception $e) {
            $this->logStep($candidate, "Ошибка обновления Google Sheets: " . $e->getMessage(), 'error', $e->getMessage());
            throw $e;
        }
    }

    protected function downloadSheetPdf(Candidate $candidate, GallupReportSheet $reportSheet)
    {
        try {
            // 1. Настройка Google клиента
            $client = new \Google\Client();
            $client->setAuthConfig(storage_path('app/google/credentials.json'));
            $client->addScope('https://www.googleapis.com/auth/drive.readonly');
            $client->useApplicationDefaultCredentials();

            $accessToken = $client->fetchAccessTokenWithAssertion()['access_token'];

            $http_build_query = [
                'format' => 'pdf',
                'portrait' => 'true',
                'size' => 'A4',
                'fitw' => 'true',
                'sheetnames' => 'false',
                'printtitle' => 'false',
                'pagenum' => 'false',
                'gridlines' => 'false',
                'fzr' => 'false',
                'horizontal_alignment' => 'CENTER',
                'top_margin' => '0.50',
                'bottom_margin' => '0.50',
                'left_margin' => '0.50',
                'right_margin' => '0.50',];

            // 2. PDF URL из Google Sheets
            $url = "https://docs.google.com/spreadsheets/d/{$reportSheet->spreadsheet_id}/export?" . http_build_query($http_build_query) . "&gid={$reportSheet->gid}";
            $url_short = "https://docs.google.com/spreadsheets/d/{$reportSheet->spreadsheet_id}/export?" . http_build_query($http_build_query) . "&gid={$reportSheet->short_gid}";

            $response = Http::withHeaders([
                'Authorization' => "Bearer $accessToken"
            ])->get($url);

            $response_short = Http::withHeaders([
                'Authorization' => "Bearer $accessToken"
            ])->get($url_short);

            if (!$response->successful() || !$response_short->successful()) {
                throw new \Exception("Ошибка при скачивании PDF: " . $response->status());
            }

            // 3. Удаляем старый отчёт, если есть
            $existing = GallupReport::where('candidate_id', $candidate->id)
                ->where('type', $reportSheet->name_report)
                ->first();

            if ($existing) {
                if ($existing->pdf_file && Storage::disk('public')->exists($existing->pdf_file)) {
                    Storage::disk('public')->delete($existing->pdf_file);
                }

                if ($existing->short_area_pdf_file && Storage::disk('public')->exists($existing->short_area_pdf_file)) {
                    Storage::disk('public')->delete($existing->short_area_pdf_file);
                }

                $existing->delete();
            }

            // 4. Генерируем пути
            $folder = 'reports/candidate_'.$candidate->id;
            $pdfFileName = str_replace(' ', '_', $candidate->full_name) . "_{$reportSheet->name_report}.pdf";
            $pdfFileName_short = str_replace(' ', '_', $candidate->full_name) . "_{$reportSheet->name_report}_short.pdf";

            $pdfPath = "{$folder}/{$pdfFileName}";
            $pdfPath_short = "{$folder}/{$pdfFileName_short}";

            // 5. Сохраняем PDF
            Storage::disk('public')->put($pdfPath, $response->body());
            Storage::disk('public')->put($pdfPath_short, $response_short->body());

            // 7. Записываем отчет
            $report = GallupReport::create([
                'candidate_id' => $candidate->id,
                'type' => $reportSheet->name_report,
                'pdf_file' => $pdfPath,
                'short_area_pdf_file' => $pdfPath_short,
            ]);
        } catch (\Exception $e) {
            $this->logStep($candidate, "Ошибка скачивания PDF: " . $e->getMessage(), 'error', $e->getMessage());
            throw $e;
        }
    }
    protected function cleanHtmlForPdf($html)
    {
        // 1. Принудительно конвертируем в UTF-8
        $html = mb_convert_encoding($html, 'UTF-8', 'UTF-8');

        // 2. Удаляем проблемные управляющие символы
        $html = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $html);

        // 3. Заменяем проблемные символы
        $html = str_replace([
            '\u00a0', // неразрывный пробел
            '&nbsp;'
        ], ' ', $html);

        // 4. Удаляем неправильные последовательности UTF-8
        $html = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F-\x9F]/u', '', $html);

        // 5. Проверяем и добавляем мета-тег для кодировки
        if (!preg_match('/<meta[^>]*charset/i', $html)) {
            if (preg_match('/<head[^>]*>/i', $html)) {
                $html = preg_replace('/(<head[^>]*>)/i', '$1<meta charset="UTF-8">', $html);
            } else {
                $html = '<meta charset="UTF-8">' . $html;
            }
        }

        // 6. Добавляем DOCTYPE если его нет
        if (!str_starts_with(trim($html), '<!DOCTYPE')) {
            $html = '<!DOCTYPE html>' . $html;
        }

        return $html;
    }
    protected function sanitizeUtf8($text)
    {
        // Удаляем невалидные UTF-8 последовательности
        $text = filter_var($text, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);

        // Принудительно валидируем UTF-8
        if (!mb_check_encoding($text, 'UTF-8')) {
            // Заменяем невалидные символы на знак вопроса
            $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
        }

        return $text;
    }

    public function mergeCandidateReportPdfs(Candidate $candidate, string $version = 'full')
    {
        $tempHtmlPdf = storage_path("app/temp_candidate_{$candidate->id}.pdf");
        // ✅ Удаляем временный PDF, если он уже существует
        if (file_exists($tempHtmlPdf)) {
            unlink($tempHtmlPdf);
        }
        // 1️⃣ Сгенерировать PDF анкеты
        $html = app(\App\Http\Controllers\CandidateReportController::class)
            ->showV2($candidate, $version)
            ->render();
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        if (!mb_check_encoding($html, 'UTF-8')) {
            dd("HTML is not valid UTF-8");
        }
        $html = $this->cleanHtmlForPdf($html);
        // Дополнительная проверка и исправление кодировки
        if (!mb_check_encoding($html, 'UTF-8')) {
            // Попытка определить и конвертировать кодировку
            $encoding = mb_detect_encoding($html, ['UTF-8', 'Windows-1251', 'ISO-8859-1'], true);
            if ($encoding && $encoding !== 'UTF-8') {
                $html = mb_convert_encoding($html, 'UTF-8', $encoding);
            } else {
                // Принудительная очистка от некорректных символов
                $html = mb_convert_encoding($html, 'UTF-8', 'UTF-8');
            }
        }

        // Финальная очистка
        $html = $this->sanitizeUtf8($html);

        $snappy = new Pdf('/usr/bin/wkhtmltopdf');


        $snappy->setOptions([
            'encoding' => 'utf-8',
            'page-size' => 'A4',
            'no-outline' => true,
            'margin-top' => '10mm',
            'margin-bottom' => '10mm',
            'margin-left' => '10mm',
            'margin-right' => '10mm',
            'disable-smart-shrinking' => true,
            'print-media-type' => true,
            // Добавляем опции для лучшей работы с UTF-8
            'load-error-handling' => 'ignore',
            'load-media-error-handling' => 'ignore',
        ]);

        try {
            $snappy->generateFromHtml($html, $tempHtmlPdf);
        } catch (\Exception $e) {
            dd([
                'message' => $e->getMessage(),
                'snippet' => mb_substr($html, 0, 1000),
            ]);
        }

        // 2️⃣ Получаем все файлы для объединения
        $pdfPaths = [$tempHtmlPdf];

        $reports = GallupReport::where('candidate_id', $candidate->id)->get();

        foreach ($reports as $report) {

            $file = $report->short_area_pdf_file;
            if (!$file) continue;

            $relative = ltrim($file, '/');

            $fullPath = Storage::disk('public')->path($relative);

            if (file_exists($fullPath)) {
                $pdfPaths[] = $fullPath;
            }
        }

        // 3️⃣ Объединяем через FPDI
        $pdfFileName = str_replace(' ', '_', $candidate->full_name) . "_full_anketa";
        $outputRelative = "reports/candidate_{$candidate->id}/{$pdfFileName}.pdf";
        $outputFull = Storage::disk('public')->path($outputRelative);

        Storage::disk('public')->makeDirectory(dirname($outputRelative));

        
        if (Storage::disk('public')->exists($candidate->anketa_pdf)) {
            Storage::disk('public')->delete($candidate->anketa_pdf);
        }

        $pdf = new Fpdi();

        foreach ($pdfPaths as $path) {
            try {
                $pageCount = $pdf->setSourceFile($path);
                for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                    $templateId = $pdf->importPage($pageNo);
                    $size = $pdf->getTemplateSize($templateId);

                    $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                    $pdf->useTemplate($templateId);
                }
            } catch (\Throwable $e) {
                Log::warning("Ошибка при объединении PDF: {$path} — " . $e->getMessage());
            }
        }

        $pdf->Output($outputFull, 'F');

        //Удаляем временный PDF
        if (file_exists($tempHtmlPdf)) {
            unlink($tempHtmlPdf);
        }

        return $outputRelative;
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
        try {
            Log::info('Импорт психотипов начат', ['report_id' => $candidate->id]);
            $spreadsheetId = $reportSheet->spreadsheet_id;
            $sheetName = 'Formula';

            // Авторизация Google
            $credentialsPath = storage_path('app/google/credentials.json');

            // Проверяем существование файла credentials
            if (!file_exists($credentialsPath)) {
                throw new \Exception("Файл credentials.json не найден в {$credentialsPath}. Необходимо создать Google Service Account и поместить JSON файл в эту папку.");
            }

            $client = new \Google\Client();
            $client->setAuthConfig($credentialsPath);
            $client->addScope(\Google\Service\Sheets::SPREADSHEETS_READONLY);
            $client->setAccessType('offline');

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
        } catch (\Exception $e) {
            $this->logStep($candidate, "Ошибка импорта формул: " . $e->getMessage(), 'error', $e->getMessage());
            throw $e;
        }
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
     * Генерирует объединенную анкету по требованию с временным хранением
     */
    public function generateAnketaPdfOnDemand(Candidate $candidate, string $version = 'full')
    {
        // Генерируем объединенный PDF
        $mergedPath = $this->mergeCandidateReportPdfs($candidate, $version);
        
        // Создаем временный файл с уникальным именем
        $tempFileName = "{$candidate->full_name}_{$version}_{$candidate->id}_" . date('d.m.Y') . ".pdf";
        $tempPath = "temp_anketas/{$tempFileName}";
        
        // Копируем во временную папку
        $tempFullPath = Storage::disk('public')->path($tempPath);
        Storage::disk('public')->makeDirectory(dirname($tempPath));
        
        copy(Storage::disk('public')->path($mergedPath), $tempFullPath);
        
        // Удаляем оригинальный объединенный файл
        Storage::disk('public')->delete($mergedPath);
        
        // Планируем удаление временного файла через 30 минут
        $this->scheduleTempFileDeletion($tempPath, 30);
        
        return $tempPath;
    }

    /**
     * Планирует удаление временного файла
     */
    protected function scheduleTempFileDeletion(string $filePath, int $minutes)
    {
        // Используем Queue с задержкой для удаления файла
        \App\Jobs\DeleteTempFile::dispatch($filePath)->delay(now()->addMinutes($minutes));
    }

    /**
     * Получить историю парсинга для кандидата
     */
    public function getParseHistory(Candidate $candidate)
    {
        $history = GallupParseHistory::getHistoryForCandidate($candidate->id);
        
        return response()->json([
            'candidate_id' => $candidate->id,
            'candidate_name' => $candidate->full_name,
            'current_step' => $candidate->step_parse_gallup,
            'history' => $history
        ]);
    }

    /**
     * Получить статистику парсинга
     */
    public function getParseStatistics()
    {
        $totalRecords = GallupParseHistory::count();
        $errorRecords = GallupParseHistory::where('status', 'error')->count();
        $completedRecords = GallupParseHistory::where('status', 'completed')->count();
        $inProgressRecords = GallupParseHistory::where('status', 'in_progress')->count();
        
        $recentErrors = GallupParseHistory::where('status', 'error')
            ->where('created_at', '>=', now()->subHours(24))
            ->with('candidate:id,full_name')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'statistics' => [
                'total_records' => $totalRecords,
                'error_records' => $errorRecords,
                'completed_records' => $completedRecords,
                'in_progress_records' => $inProgressRecords,
            ],
            'recent_errors' => $recentErrors
        ]);
    }


}
