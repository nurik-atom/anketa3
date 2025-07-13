<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\GallupReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Spatie\PdfToImage\Pdf;
use Spatie\PdfToImage\Enums\OutputFormat;

class CandidateReportController extends Controller
{
    public function show(Candidate $candidate)
    {
        // Загружаем связанные данные
        $candidate->load(['gallupTalents', 'gallupReports', 'user.gardnerTestResult']);
        
        // Подготавливаем URL фото
        $photoUrl = null;
        if ($candidate->photo && Storage::disk('public')->exists($candidate->photo)) {
            $photoUrl = Storage::disk('public')->url($candidate->photo);
        }
        
        // Подготавливаем данные для отображения
        return view('candidates.report', compact('candidate', 'photoUrl'));
    }
    
    public function showV2(Candidate $candidate)
    {
        // Загружаем связанные данные
        $candidate->load(['gallupTalents', 'gallupReports', 'user.gardnerTestResult']);
        
        // Подготавливаем URL фото
        $photoUrl = null;
        if ($candidate->photo && Storage::disk('public')->exists($candidate->photo)) {
            $photoUrl = Storage::disk('public')->url($candidate->photo);
        }
        
        // Парсим содержимое PDF файлов Gallup отчетов
        $gallupPdfContents = $this->parseGallupPdfContents($candidate);
        
        // Подготавливаем данные для отображения (новая версия)
        return view('candidates.report-v2', compact('candidate', 'photoUrl', 'gallupPdfContents'));
    }
    
    /**
     * Конвертирует PDF файлы Gallup отчетов в изображения
     */
    private function parseGallupPdfContents(Candidate $candidate): array
    {
        $pdfContents = [];
        
        foreach ($candidate->gallupReports as $report) {
            if (!Storage::disk('public')->exists($report->pdf_file)) {
                continue;
            }
            
            try {
                $images = $this->convertPdfToImages($report, $candidate);
                
                $pdfContents[] = [
                    'type' => $report->type,
                    'images' => $images,
                    'file_path' => $report->pdf_file,
                    'created_at' => $report->created_at
                ];
                
            } catch (\Exception $e) {
                Log::error('Error converting PDF to images for candidate report', [
                    'candidate_id' => $candidate->id,
                    'report_id' => $report->id,
                    'error' => $e->getMessage()
                ]);
                
                // Добавляем запись об ошибке
                $pdfContents[] = [
                    'type' => $report->type,
                    'images' => [],
                    'file_path' => $report->pdf_file,
                    'created_at' => $report->created_at,
                    'error' => true,
                    'error_message' => $e->getMessage()
                ];
            }
        }
        
        return $pdfContents;
    }
    
    /**
     * Конвертирует PDF в изображения
     */
    private function convertPdfToImages(GallupReport $report, Candidate $candidate): array
    {
        $images = [];
        $pdfPath = storage_path('app/public/' . $report->pdf_file);
        
        // Создаем папку для изображений если она не существует
        $imageFolder = 'reports/images/' . $candidate->id . '/' . $report->type;
        $imageFolderPath = storage_path('app/public/' . $imageFolder);
        
        if (!is_dir($imageFolderPath)) {
            mkdir($imageFolderPath, 0755, true);
        }
        
        // Проверяем, есть ли уже сконвертированные изображения
        $existingImages = $this->getExistingImages($imageFolder, $report);
        if (!empty($existingImages)) {
            return $existingImages;
        }
        
        // Конвертируем PDF в изображения
        try {
            // Создаем экземпляр класса Pdf с настройками для версии 3.x
            $pdf = new Pdf($pdfPath);
            
            // Настраиваем параметры конвертации
            $pdf = $pdf->resolution(150)           // Разрешение для хорошего качества
                      ->format(OutputFormat::Jpg) // Формат изображения
                      ->quality(90);               // Качество изображения
            
            // Получаем количество страниц
            $pageCount = $pdf->pageCount();
            
            for ($pageNumber = 1; $pageNumber <= $pageCount; $pageNumber++) {
                $imageName = $this->generateImageName($report, $pageNumber);
                $imagePath = $imageFolderPath . '/' . $imageName;
                
                // Конвертируем страницу в изображение
                $pdf->selectPage($pageNumber)->save($imagePath);
                
                // Добавляем относительный путь к изображению
                $images[] = [
                    'path' => $imageFolder . '/' . $imageName,
                    'page' => $pageNumber,
                    'url' => Storage::disk('public')->url($imageFolder . '/' . $imageName)
                ];
            }
            
        } catch (\Exception $e) {
            Log::error('Error in PDF to image conversion', [
                'pdf_path' => $pdfPath,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
        
        return $images;
    }
    
    /**
     * Проверяет наличие уже сконвертированных изображений
     */
    private function getExistingImages(string $imageFolder, GallupReport $report): array
    {
        $images = [];
        $imageFolderPath = storage_path('app/public/' . $imageFolder);
        
        if (!is_dir($imageFolderPath)) {
            return [];
        }
        
        $files = glob($imageFolderPath . '/' . $report->type . '_page_*.jpg');
        
        foreach ($files as $file) {
            $fileName = basename($file);
            preg_match('/page_(\d+)\.jpg/', $fileName, $matches);
            $pageNumber = isset($matches[1]) ? intval($matches[1]) : 1;
            
            $images[] = [
                'path' => $imageFolder . '/' . $fileName,
                'page' => $pageNumber,
                'url' => Storage::disk('public')->url($imageFolder . '/' . $fileName)
            ];
        }
        
        // Сортируем по номеру страницы
        usort($images, function($a, $b) {
            return $a['page'] - $b['page'];
        });
        
        return $images;
    }
    
    /**
     * Генерирует имя для изображения
     */
    private function generateImageName(GallupReport $report, int $pageNumber): string
    {
        return $report->type . '_page_' . $pageNumber . '.jpg';
    }

    
    public function pdf(Candidate $candidate)
    {
        // Для будущей генерации PDF
        $candidate->load(['gallupTalents', 'gallupReports', 'user.gardnerTestResult']);
        
        $photoUrl = null;
        if ($candidate->photo && Storage::disk('public')->exists($candidate->photo)) {
            $photoUrl = Storage::disk('public')->url($candidate->photo);
        }
        
        // Здесь можно добавить логику генерации PDF
        return view('candidates.report-pdf', compact('candidate', 'photoUrl'));
    }
    
    public function downloadGallup(Candidate $candidate)
    {
        // Проверяем, есть ли Gallup файл у кандидата
        if (!$candidate->gallup_pdf || !Storage::disk('public')->exists($candidate->gallup_pdf)) {
            abort(404, 'Gallup файл не найден');
        }
        
        $filePath = storage_path('app/public/' . $candidate->gallup_pdf);
        $fileName = "gallup_{$candidate->full_name}_{$candidate->id}.pdf";
        
        return response()->download($filePath, $fileName);
    }
    
    public function downloadGallupReport(Candidate $candidate, string $type)
    {
        // Ищем отчет указанного типа для кандидата
        $report = $candidate->gallupReports()->where('type', $type)->first();
        
        if (!$report || !Storage::disk('public')->exists($report->pdf_file)) {
            abort(404, "Отчет типа {$type} не найден");
        }
        
        $filePath = storage_path('app/public/' . $report->pdf_file);
        $fileName = "gallup_report_{$type}_{$candidate->full_name}_{$candidate->id}.pdf";
        
        return response()->download($filePath, $fileName);
    }
}
