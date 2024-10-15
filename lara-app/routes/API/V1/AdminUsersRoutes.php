<?php

use App\Http\Controllers\API\V1\Users\UserStatsController;

Route::middleware(['auth:sanctum', 'role:admin'])->name('admins.users.')->prefix('admins/users')->group(function () {
    Route::get('/{user}/stats', UserStatsController::class)->name('stats');
});
