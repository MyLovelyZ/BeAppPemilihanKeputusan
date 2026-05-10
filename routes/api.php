<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [\App\Http\Controllers\AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [\App\Http\Controllers\AuthController::class, 'logout']);
});

Route::middleware(['auth:sanctum', 'superadmin'])->group(function () {
    Route::get('/superadmin-only', function () {
        return response()->json([
            'message' => 'Welcome, Super Admin!'
        ]);
    });
});