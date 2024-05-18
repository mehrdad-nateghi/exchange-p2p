<?php

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\API\V1\Auth\ResendCodeController;
use App\Http\Controllers\API\V1\Auth\SendCodeController;
use App\Http\Controllers\API\V1\Auth\SetPasswordController;
use App\Http\Controllers\API\V1\Auth\SignUpController;

// Guest
Route::name('auth.')->prefix('auth')->group(function () {
    Route::post('/send-code',SendCodeController::class)->name('send-code');
    Route::post('/resend-code',ResendCodeController::class)->name('resend-code');
    Route::post('/signup',SignUpController::class)->name('signup');
});

// Auth
Route::middleware('auth:api')->name('auth.')->prefix('auth')->group(function () {
    Route::post('/set-password',SetPasswordController::class)->name('set-password');
});
