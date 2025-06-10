<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CandidateController;
use App\Http\Controllers\DashboardController;

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
});
