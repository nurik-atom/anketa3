<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\GallupReport;
use App\Models\GallupReportSheet;
use App\Models\GallupReportSheetIndex;
use App\Models\GallupReportSheetValue;
use Google\Client;
use Google\Service\Drive;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;
use setasign\Fpdi\Fpdi;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\Storage;
use Google\Service\Sheets;
use Spatie\Browsershot\Browsershot;
use Spatie\LaravelPdf\Facades\Pdf;

class GallupController extends Controller
{
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
                $reportSheet
            );
        }

        // 👇 Объединение всех PDF после скачивания
        $mergedPath = $this->mergeCandidateReportPdfs($candidate);

        // если надо сохранить путь в модель:
        $candidate->anketa_pdf = $mergedPath;
        $candidate->save();

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

    protected function downloadSheetPdf(Candidate $candidate, GallupReportSheet $reportSheet)
    {
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


    }

    public function mergeCandidateReportPdfs(Candidate $candidate)
    {
        $tempHtmlPdf = storage_path("app/temp_candidate_{$candidate->id}.pdf");

        // 1️⃣ Сгенерировать PDF анкеты
        $html = app(\App\Http\Controllers\CandidateReportController::class)
            ->showV2($candidate)
            ->render();



        Browsershot::html($html)
            ->setOption('args', ['--no-sandbox'])
            ->setOption('executablePath', '/usr/bin/chromium-browser')
            ->save($tempHtmlPdf);

//        Pdf::html($html)
////            ->withBrowsershotOption('args', ['--no-sandbox'])
//            ->save($tempHtmlPdf);

//        Pdf::html($html)->withBrowsershotOption('args', ['--no-sandbox'])->save($tempHtmlPdf);

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

        if (file_exists($outputFull)) {
            unlink($outputFull);
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


}
