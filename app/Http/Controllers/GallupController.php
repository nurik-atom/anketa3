<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\GallupReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\Storage;
use Google\Client;
use Google\Service\Sheets;

class GallupController extends Controller
{

    // этот метод только для тестирования, чтобы проверить, что всё работает
    public function process(Request $request)
    {
        // Шаг 1: загрузка файла
        $path = $request->file('pdf')->store('gallup', 'public');

        // Шаг 2: Извлечение текста
        $parser = new Parser();
        $pdf = $parser->parseFile(storage_path('app/public/' . $path));
        $text = $pdf->getText();

        // Шаг 3: Извлечение списка талантов
        preg_match_all('/\d{1,2}\.\s+(.*)/', $text, $matches);
        $talents = $matches[1] ?? [];

        // Пример: сохранить топ-10 в лог
//        logger()->info('Top 10 Talents:', array_slice($talents, 0, 34));

        return response()->json([
            'top_10_talents' => array_slice($talents, 0, 34),
        ]);
    }

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
            $hasCorrectPageCount = count($pages) === 26;
            $containsCliftonHeader = str_contains($text, 'Gallup, Inc. All rights reserved.');
            $containsGallupCopyright = str_contains($text, 'Gallup, Inc.');
            $containsTalentList = preg_match('/1\.\s+[A-Za-z-]+/', $text);

            return $hasCorrectPageCount && $containsCliftonHeader && $containsGallupCopyright && $containsTalentList;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function parseGallupFromCandidateFile(Candidate $candidate)
    {
        if (!$candidate->gallup_pdf || !Storage::disk('public')->exists($candidate->gallup_pdf)) {
            return response()->json(['error' => 'Файл не найден.'], 404);
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

        // DPs Отчет
        // Обновление Google Sheets
        $this->updateGoogleSheetByCellMap($candidate, $talents, Config::get('app.google_sheet.dps.spreadsheet_id'));
        // Скачивание PDF листа Main (Russian)
        $this->downloadSheetPdf(
            $candidate,
            Config::get('app.google_sheet.dps.spreadsheet_id'), // Spreadsheet ID
            Config::get('app.google_sheet.dps.gid'), // GID листа Main (Russian)
            'DPs');

        // DPT Отчет
        $this->updateGoogleSheetByCellMap($candidate, $talents, Config::get('app.google_sheet.dpt.spreadsheet_id'));
        $this->downloadSheetPdf(
            $candidate,
            Config::get('app.google_sheet.dpt.spreadsheet_id'), // Spreadsheet ID
            Config::get('app.google_sheet.dpt.gid'), // GID листа Main (Russian)
            'DPT');

        // FMD Отчет
        $this->updateGoogleSheetByCellMap($candidate, $talents, Config::get('app.google_sheet.fmd.spreadsheet_id'));
        $this->downloadSheetPdf(
            $candidate,
            Config::get('app.google_sheet.fmd.spreadsheet_id'), // Spreadsheet ID
            Config::get('app.google_sheet.fmd.gid'), // GID листа Main (Russian)
            'FMD');

        return response()->json([
            'message' => 'Данные Gallup обновлены, Google Sheet заполнен, PDF сохранён.',
        ]);
    }

    protected function updateGoogleSheetByCellMap(Candidate $candidate, array $talents, $spreadsheetId)
    {
        $client = new \Google\Client();
        $client->setAuthConfig(storage_path('app/google/credentials.json'));
        $client->addScope(\Google\Service\Sheets::SPREADSHEETS);
        $client->useApplicationDefaultCredentials();

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

}
