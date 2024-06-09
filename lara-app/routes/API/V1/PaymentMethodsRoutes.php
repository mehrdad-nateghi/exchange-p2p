<?php

/*
|--------------------------------------------------------------------------
| User's Payment methods Routes
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\API\V1\PaymentMethods\DeletePaymentMethodController;
use App\Http\Controllers\API\V1\PaymentMethods\IndexPaymentMethodController;
use App\Http\Controllers\API\V1\PaymentMethods\ShowPaymentMethodController;
use App\Http\Controllers\API\V1\PaymentMethods\StorePaymentMethodController;

// todo-mn: add middleware for applicant
Route::middleware('auth:api')->name('users.payment-methods.')->prefix('users/payment-methods')->group(function () {
    Route::get('/',IndexPaymentMethodController::class)->name('index');
    Route::middleware('can:view,paymentMethod')->get('/{paymentMethod}',ShowPaymentMethodController::class)->name('show');
    Route::post('/',StorePaymentMethodController::class)->name('store');
    Route::middleware('can:delete,paymentMethod')->delete('/{paymentMethod}',DeletePaymentMethodController::class)->name('delete');
});