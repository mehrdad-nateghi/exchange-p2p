<?php

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\API\V1\Auth\LoginController;
use App\Http\Controllers\API\V1\Auth\LogOutController;
use App\Http\Controllers\API\V1\Auth\RefreshTokenController;
use App\Http\Controllers\API\V1\Auth\ResendCodeController;
use App\Http\Controllers\API\V1\Auth\SendCodeController;
use App\Http\Controllers\API\V1\Auth\SetPasswordController;
use App\Http\Controllers\API\V1\Auth\SignUpController;
use App\Http\Controllers\API\V1\Auth\VerifyCodeController;

// Guest
Route::name('auth.')->prefix('auth')->group(function () {
    Route::post('/send-code',SendCodeController::class)->name('send-code');
    Route::post('/resend-code',ResendCodeController::class)->name('resend-code');
    Route::post('/verify-code',VerifyCodeController::class)->name('verify-code');
    Route::post('/signup',SignUpController::class)->name('signup');
    Route::post('/login',LoginController::class)->name('login');
});

// Auth
Route::middleware('auth:api')->name('auth.')->prefix('auth')->group(function () {
    Route::post('/set-password',SetPasswordController::class)->name('set-password');
    Route::post('/logout',LogOutController::class)->name('logout');
});

Route::post('/refresh-token',RefreshTokenController::class)->name('refresh-token');

