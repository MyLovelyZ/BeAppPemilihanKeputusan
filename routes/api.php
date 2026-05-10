<?php

use App\Http\Controllers\AdminManagementController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout']);

    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'index']);
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update']);
    Route::delete('/profile', [\App\Http\Controllers\ProfileController::class, 'destroy']);
});

Route::middleware(['auth:sanctum', 'superadmin'])->group(function () {
    Route::get('superadmin/admins', [\App\Http\Controllers\AdminManagementController::class, 'index']);
    Route::post('superadmin/admins', [\App\Http\Controllers\AdminManagementController::class, 'store']);
    Route::delete('superadmin/admins/{id}', [\App\Http\Controllers\AdminManagementController::class, 'destroy']);

    Route::get('superadmin/admins/{id}/quizzes', [AdminManagementController::class, 'adminQuizzes']);
    Route::patch('superadmin/admins/{id}/ban', [AdminManagementController::class, 'banAdmin']);
    Route::delete('superadmin/admins/quizzes/{id}', [AdminManagementController::class, 'deleteAdminQuiz']);
});