<?php

use App\Http\Controllers\GallupController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/upload-photo', [App\Http\Controllers\PhotoController::class, 'upload']);
});
Route::post('/gallup/process', [GallupController::class, 'process']);

Route::post('/candidates/parse-gallup/{candidate}', [GallupController::class, 'parseGallupFromCandidateFile']);
Route::post('/candidates/merge-gallup/{candidate}', [GallupController::class, 'mergeCandidateReportPdfs']);
