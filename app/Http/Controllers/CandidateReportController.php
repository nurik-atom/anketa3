<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CandidateReportController extends Controller
{
    public function show(Candidate $candidate)
    {
        // Загружаем связанные данные
        $candidate->load(['gallupTalents', 'gallupReports']);
        
        // Подготавливаем URL фото
        $photoUrl = null;
        if ($candidate->photo && Storage::disk('public')->exists($candidate->photo)) {
            $photoUrl = Storage::disk('public')->url($candidate->photo);
        }
        
        // Подготавливаем данные для отображения
        return view('candidates.report', compact('candidate', 'photoUrl'));
    }
    
    public function pdf(Candidate $candidate)
    {
        // Для будущей генерации PDF
        $candidate->load(['gallupTalents', 'gallupReports']);
        
        $photoUrl = null;
        if ($candidate->photo && Storage::disk('public')->exists($candidate->photo)) {
            $photoUrl = Storage::disk('public')->url($candidate->photo);
        }
        
        // Здесь можно добавить логику генерации PDF
        return view('candidates.report-pdf', compact('candidate', 'photoUrl'));
    }
}
