<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\GallupReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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

        // Подготавливаем данные для отображения (новая версия)
        return view('candidates.report-v2', compact('candidate','photoUrl'));
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

    public function dowloadAnketaReport(Candidate $candidate){
        $filePath = storage_path("app/public/" . $candidate->anketa_pdf);

        if (!file_exists($filePath)) {
            abort(404, 'Файл не найден');
        }

        return response()->download($filePath);
    }
}
