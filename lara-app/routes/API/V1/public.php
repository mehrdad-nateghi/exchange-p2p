<?php

/*
|--------------------------------------------------------------------------
| Global Routes
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\API\V1\Public\HealthCheckController;

Route::name('public.')->prefix('public')->group(function () {
    Route::get('/health/check',HealthCheckController::class)->name('health.check');
});
