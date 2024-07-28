<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('send-otp', [AuthController::class, 'sendOtp']);
Route::post('verify-password-otp', [AuthController::class, 'verifyForgotPasswordOtp']);
Route::post('reset-password', [AuthController::class, 'resetPassword']);

Route::middleware(['auth:api'])->group(function () {
    Route::get('users', [AuthController::class, 'index']);
    Route::get('users/{id}', [AuthController::class, 'show']);
    Route::put('users/{id}', [AuthController::class, 'update']);
    Route::delete('users/{id}', [AuthController::class, 'destroy']);
    Route::post('verify-otp', [AuthController::class, 'verifyOtp']);
});