<?php

/*
|--------------------------------------------------------------------------
| Global Routes
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\API\V1\Public\DailyRateRangeController;
use App\Http\Controllers\API\V1\Public\GatewayCallbackController;
use App\Http\Controllers\API\V1\Public\HealthCheckController;
use Shetabit\Multipay\Invoice;
use Shetabit\Payment\Facade\Payment;

Route::name('global.')->group(function () {
    Route::get('/health',HealthCheckController::class)->name('health.check');
    Route::get('/daily-rate-range',DailyRateRangeController::class)->name('daily.rate.range');
    Route::get('/daily-rate-range',DailyRateRangeController::class)->name('daily.rate.range');
    Route::get('/gateway/callback',GatewayCallbackController::class)->name('gateway.callback');
});
