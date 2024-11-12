<?php

use App\Http\Controllers\API\V1\Users\Admin\LoginAsAdminController;
use App\Http\Controllers\API\V1\Users\Admin\LoginAsUserController;
use App\Http\Controllers\API\V1\Users\Admin\UserStatsController;

Route::middleware(['auth:sanctum', 'role:admin'])->name('admins.users.')->prefix('admins/users')->group(function () {
    Route::get('/{user}/stats', UserStatsController::class)->name('stats');
    Route::get('/login-as-user/{user}', LoginAsUserController::class);
    Route::get('/login-as-admin', LoginAsAdminController::class);
});
