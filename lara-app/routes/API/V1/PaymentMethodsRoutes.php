<?php

/*
|--------------------------------------------------------------------------
| Payment methods Routes
|--------------------------------------------------------------------------
*/


use App\Http\Controllers\API\V1\PaymentMethods\StorePaymentMethodController;

Route::middleware('auth:api')->name('payment-methods.')->prefix('payment-methods')->group(function () {
    Route::post('/',StorePaymentMethodController::class)->name('payment-methods.store');
});
