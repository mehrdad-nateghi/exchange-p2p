<?php

/*
|--------------------------------------------------------------------------
| Global Routes
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\API\V1\Public\DailyRateRangeController;
use App\Http\Controllers\API\V1\Public\HealthCheckController;
use Shetabit\Multipay\Invoice;
use Shetabit\Payment\Facade\Payment;

Route::name('global.')->group(function () {
    Route::get('/health',HealthCheckController::class)->name('health.check');
    Route::get('/daily-rate-range',DailyRateRangeController::class)->name('daily.rate.range');
    Route::get('/daily-rate-range',DailyRateRangeController::class)->name('daily.rate.range');

    Route::get('/test', function () {
        // Create new invoice.
        $invoice = (new Invoice)->amount(1000);

        return Payment::purchase($invoice, function($driver, $transactionId) {
            // Store transactionId in database as we need it to verify payment in the future.
        })->pay()->render();

        /*return Payment::purchase(
            (new Invoice)->amount(1000),
            function($driver, $transactionId) {
                // Store transactionId in database.
                // We need the transactionId to verify payment in the future.
            }
        )->pay()->toJson();*/
// Purchase the given invoice.
        /* Payment::purchase($invoice,function($driver, $transactionId) {
             // We can store $transactionId in database.
         });*/

        $paymentResponse = Payment::purchase($invoice, function($driver, $transactionId) {
                // Store $transactionId in your database if needed
            })->pay();

        dd($paymentResponse);

        $paymentUrl = Payment::callbackUrl(route('payment.callback'))
            ->purchase($invoice, function($driver, $transactionId) {
                // Store $transactionId in your database if needed
            })->pay()->getTargetUrl();

        // Instead of returning JSON, we'll return the HTML content
        return response($paymentResponse->getRedirectHtmlForm());

        return view('welcome');
    });
});
