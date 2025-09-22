<?php

use App\Http\Controllers\Auth\OtpRequestController;
use App\Http\Controllers\Auth\OtpVerifyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// OTP Authentication API endpoints
Route::prefix('auth')->group(function () {
    Route::post('otp/request', [OtpRequestController::class, 'store']);
    Route::post('otp/verify', [OtpVerifyController::class, 'store']);
});

