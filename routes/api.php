<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\ClaimController;
use App\Http\Controllers\ChatbotController;

// Public Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/verify-email', [AuthController::class, 'verifyEmail']);
Route::post('/resend-verification', [AuthController::class, 'resendVerification']);
Route::get('/categories', [ReportController::class, 'categories']);
Route::get('/campuses', [ReportController::class, 'campuses']);
Route::get('/reports', [ReportController::class, 'index']);
Route::post('/chatbot', [ChatbotController::class, 'ask']);

// Protected Routes (Sanctum Token Required)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Reports
    Route::post('/reports', [ReportController::class, 'store']);
    Route::get('/my-reports', [ReportController::class, 'myReports']);

    // Claims
    Route::post('/claims/{reportId}', [ClaimController::class, 'store']);
    Route::get('/my-claims', [ClaimController::class, 'myClaims']);
    Route::delete('/claims/{id}', [ClaimController::class, 'cancel']);
});

