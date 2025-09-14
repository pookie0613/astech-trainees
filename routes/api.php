<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TraineeController;
use App\Http\Controllers\ResultController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Trainees API
Route::apiResource('trainees', TraineeController::class);

// Additional trainee routes
Route::prefix('trainees/{trainee}')->group(function () {
    Route::post('/enroll', [TraineeController::class, 'enrollInClass']);
    Route::delete('/unenroll/{class}', [TraineeController::class, 'removeFromClass']);
    Route::get('/classes', [TraineeController::class, 'getClasses']);
    Route::get('/results', [TraineeController::class, 'getResults']);
});

// Results API
Route::apiResource('results', ResultController::class);

// Additional result routes
Route::get('/results/trainee/{trainee}', [ResultController::class, 'getByTrainee']);
Route::get('/results/exam/{exam}', [ResultController::class, 'getByExam']);
Route::post('/results/bulk', [ResultController::class, 'bulkCreate']);

// Health check
Route::get('/health', function () {
    return response()->json(['status' => 'healthy', 'service' => 'trainees']);
});
