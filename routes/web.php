<?php

use App\Http\Controllers\GallupController;
use App\Models\Candidate;
use App\Models\GallupReportSheet;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CandidateReportController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;

Route::get('/', function () {
    return view('welcome');
});

// Перенаправление /login на главную страницу (welcome)
Route::get('/login', function () {
    return redirect('/');
})->name('login');

// Кастомный роут для сброса пароля с перенаправлением на главную страницу
Route::post('/reset-password', function (Illuminate\Http\Request $request) {
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:8|confirmed',
    ]);

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, $password) {
            $user->forceFill([
                'password' => Hash::make($password)
            ])->save();
        }
    );

    if ($status === Password::PASSWORD_RESET) {
        return redirect('/')->with('status', 'Пароль успешно сброшен! Теперь вы можете войти с новым паролем.');
    }

    return back()->withErrors(['email' => [__($status)]]);
})->name('password.update');

Route::get('/candidate/{candidate}/report', [CandidateReportController::class, 'showV2'])->name('candidate.report');

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/candidate/form/{id?}', [CandidateController::class, 'create'])->name('candidate.form');
    Route::get('/candidate/test', [CandidateController::class, 'test'])->name('candidate.test');

    // Отчеты кандидатов

//    Route::get('/candidate/{candidate}/report/v2', [CandidateReportController::class, 'showV2'])->name('candidate.report.v2');
    Route::get('/candidate/{candidate}/report/pdf', [CandidateReportController::class, 'pdf'])->name('candidate.report.pdf');
    Route::get('/candidate/{candidate}/gallup/download', [CandidateReportController::class, 'downloadGallup'])->name('candidate.gallup.download');
    Route::get('/candidate/{candidate}/gallup-report/{type}/download', [CandidateReportController::class, 'downloadGallupReport'])->name('candidate.gallup-report.download');
    Route::get('/candidate/{candidate}/downloadAnketa', [CandidateReportController::class, 'dowloadAnketaReport'])->name('candidate.anketa.download');

    // Тест Гарднера - основной роут (все вопросы сразу)
    Route::get('/gardner-test', function () {
        return view('candidate.gardner-test-all');
    })->name('gardner-test');

    // ЗАКОММЕНТИРОВАННЫЙ роут - постраничный режим
    // Route::get('/gardner-test-all', function () {
    //     return view('candidate.gardner-test-all');
    // })->name('gardner-test-all');

    // Создание Google Docs из нескольких Google Sheets
    Route::post('/candidate/{candidate}/create-google-docs', [GallupController::class, 'createGoogleDocsFromSheets'])->name('candidate.create-google-docs');
    Route::post('/candidate/{candidate}/create-google-docs-advanced', [GallupController::class, 'createGoogleDocsFromSheetsAdvanced'])->name('candidate.create-google-docs-advanced');

    // Создание Word документа
    Route::post('/candidate/{candidate}/create-word-document', [GallupController::class, 'createWordDocument'])->name('candidate.create-word-document');
    Route::get('/candidate/{candidate}/download-word-document/{filename}', [GallupController::class, 'downloadWordDocument'])->name('candidate.download-word-document');

    // Конвертация PDF в Word
    Route::post('/candidate/{candidate}/convert-pdf-to-word/{reportType}', [GallupController::class, 'convertPdfToWordApi'])->name('candidate.convert-pdf-to-word');

    // Тестовый роут для создания Word документа
    Route::get('/candidate/{candidate}/test-word-document', [GallupController::class, 'testWordDocument'])->name('candidate.test-word-document');


});

Route::post('/gallup/process', [GallupController::class, 'process']);

// Тестовые роуты для ручной отправки Job в очередь
Route::get('/test-gallup-job/{candidate}', function(App\Models\Candidate $candidate) {
    App\Jobs\ProcessGallupFile::dispatch($candidate);
    return response()->json(['message' => 'ProcessGallupFile Job dispatched successfully', 'candidate_id' => $candidate->id]);
})->middleware('auth');

// Тестовый роут для скачивания Word документа отдельно
Route::get('/test-word-download-job/{candidate}/{reportType?}', function(App\Models\Candidate $candidate, $reportType = 'Individual Report') {
    // Получаем первый доступный лист отчета для тестирования
    $reportSheet = \App\Models\GallupReportSheet::first();

    if (!$reportSheet) {
        return response()->json(['error' => 'No report sheets found. Please run gallup seeder first.'], 404);
    }

    // Находим существующий отчет или создаем временную запись
    $report = \App\Models\GallupReport::where('candidate_id', $candidate->id)
        ->where('type', $reportType)
        ->first();

    // Запускаем Job для скачивания Word документа
    \App\Jobs\DownloadGallupWordDocument::dispatch(
        $candidate,
        $reportSheet->spreadsheet_id,
        $reportSheet->gid,
        $reportType,
        $report
    );

    return response()->json([
        'message' => 'DownloadGallupWordDocument Job dispatched successfully',
        'candidate_id' => $candidate->id,
        'report_type' => $reportType,
        'spreadsheet_id' => $reportSheet->spreadsheet_id,
        'gid' => $reportSheet->gid
    ]);
})->middleware('auth');



Route::get('/import-formula/{sheetId}/{candidateId}', function ($sheetId, $candidateId) {
    $sheet = GallupReportSheet::findOrFail($sheetId);
    $candidate = Candidate::findOrFail($candidateId);

    app(GallupController::class)->importFormulaValues($sheet, $candidate);

    return 'Импорт завершён';
});
