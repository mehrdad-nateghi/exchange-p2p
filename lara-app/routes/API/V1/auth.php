<?php

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/


use App\Http\Controllers\API\V1\Auth\SendCodeController;
use App\Http\Controllers\API\V1\Auth\SignUpController;

//use App\Http\Controllers\API\V1\Auth\SignupController;

Route::name('auth.')->prefix('auth')->group(function () {
    Route::post('/send-code',SendCodeController::class)->name('send-code');
    Route::post('/signup',SignUpController::class)->name('signup');
});
