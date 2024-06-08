<?php

/*
|--------------------------------------------------------------------------
| Payment methods Routes
|--------------------------------------------------------------------------
*/


use App\Http\Controllers\API\V1\PaymentMethods\IndexPaymentMethodController;
use App\Http\Controllers\API\V1\PaymentMethods\ShowPaymentMethodController;
use App\Http\Controllers\API\V1\PaymentMethods\StorePaymentMethodController;

Route::middleware('auth:api')->name('payment-methods.')->prefix('payment-methods')->group(function () {
    Route::get('/',IndexPaymentMethodController::class)->name('payment-methods.index');
    Route::post('/',StorePaymentMethodController::class)->name('payment-methods.store'); // todo-mn: just for admin users
});


Route::middleware('auth:api')->name('users.')->prefix('users')->group(function () {
    Route::get('/{user}/payment-methods',ShowPaymentMethodController::class)->name('payment-methods.show');
});
