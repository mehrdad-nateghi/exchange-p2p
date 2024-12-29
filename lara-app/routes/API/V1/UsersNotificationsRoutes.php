<?php

use App\Http\Controllers\API\V1\Notifications\Users\ShowNotificationController;
use App\Http\Controllers\API\V1\Notifications\Users\StreamNotificationsController;

Route::middleware('auth:sanctum')->name('users.notifications')->prefix('users/notifications')->group(function () {
    Route::get('/stream', StreamNotificationsController::class)->name('stream');
    Route::middleware('can:view,notification')->get('/{notification}', ShowNotificationController::class)->name('show');

});
