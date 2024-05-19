<?php

/*
|--------------------------------------------------------------------------
| User Routes
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\API\V1\Auth\ResendCodeController;
use App\Http\Controllers\API\V1\Auth\SendCodeController;
use App\Http\Controllers\API\V1\Auth\SetPasswordController;
use App\Http\Controllers\API\V1\Users\MeController;

// Auth
Route::middleware('auth:api')->name('users.')->prefix('users')->group(function () {
    Route::get('/me',MeController::class)->name('me');
});
