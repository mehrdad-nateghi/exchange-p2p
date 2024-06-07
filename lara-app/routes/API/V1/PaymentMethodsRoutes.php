<?php

/*
|--------------------------------------------------------------------------
| Payment methods Routes
|--------------------------------------------------------------------------
*/


use App\Http\Controllers\API\V1\PaymentMethods\IndexPaymentMethodController;
use App\Http\Controllers\API\V1\PaymentMethods\StorePaymentMethodController;

Route::middleware('auth:api')->name('payment-methods.')->prefix('payment-methods')->group(function () {
    Route::get('/',IndexPaymentMethodController::class)->name('payment-methods.index');
    Route::post('/',StorePaymentMethodController::class)->name('payment-methods.store'); // todo-mn: just for admin users
    //Route::post('/users/{user}/payment-methods',StorePaymentMethodController::class)->name('payment-methods.store');
});
