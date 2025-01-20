<?php

/*
|--------------------------------------------------------------------------
| User Routes
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\API\V1\Test\SendTestNotificationController;
use App\Http\Controllers\API\V1\Users\MeController;
use App\Http\Controllers\API\V1\Users\User\ChangePasswordController;

Route::middleware('auth:sanctum')->name('users.')->prefix('users')->group(function () {
    Route::get('/me',MeController::class)->name('me');
    Route::post('/change-password',ChangePasswordController::class)->name('change.password');
    Route::get('/send-test-notification',SendTestNotificationController::class)->name('send.test.notification');
});
