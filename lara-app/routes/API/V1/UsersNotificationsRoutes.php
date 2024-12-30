<?php

use App\Http\Controllers\API\V1\Notifications\Users\ShowNotificationController;
use App\Http\Controllers\API\V1\Notifications\Users\StreamNotificationsController;
use App\Http\Controllers\API\V1\Notifications\Users\UpdateNotificationReadAtController;

Route::middleware('auth:sanctum')->name('users.notifications')->prefix('users/notifications')->group(function () {
    Route::get('/stream', StreamNotificationsController::class)->name('stream');
    //Route::middleware('can:view,notification')->get('/{notification}', ShowNotificationController::class)->name('show');
    Route::middleware('can:update,notification')->patch('/{notification}/read', UpdateNotificationReadAtController::class)->name('update.read_at');
});
