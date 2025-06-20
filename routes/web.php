<?php

use App\Http\Controllers\GallupController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CandidateReportController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/candidate/form/{id?}', [CandidateController::class, 'create'])->name('candidate.form');
    Route::get('/candidate/test', [CandidateController::class, 'test'])->name('candidate.test');
    
    // Отчеты кандидатов
    Route::get('/candidate/{candidate}/report', [CandidateReportController::class, 'show'])->name('candidate.report');
    Route::get('/candidate/{candidate}/report/pdf', [CandidateReportController::class, 'pdf'])->name('candidate.report.pdf');
    Route::get('/candidate/{candidate}/gallup/download', [CandidateReportController::class, 'downloadGallup'])->name('candidate.gallup.download');
    Route::get('/candidate/{candidate}/gallup-report/{type}/download', [CandidateReportController::class, 'downloadGallupReport'])->name('candidate.gallup-report.download');
    
    // Тест Гарднера - основной роут (все вопросы сразу)
    Route::get('/gardner-test', function () {
        return view('candidate.gardner-test-all');
    })->name('gardner-test');
    
    // ЗАКОММЕНТИРОВАННЫЙ роут - постраничный режим
    // Route::get('/gardner-test-all', function () {
    //     return view('candidate.gardner-test-all');
    // })->name('gardner-test-all');
});

Route::post('/gallup/process', [GallupController::class, 'process']);

// Тестовый роут для ручной отправки Job в очередь
Route::get('/test-gallup-job/{candidate}', function(App\Models\Candidate $candidate) {
    App\Jobs\ProcessGallupFile::dispatch($candidate);
    return response()->json(['message' => 'Job dispatched successfully', 'candidate_id' => $candidate->id]);
})->middleware('auth');
