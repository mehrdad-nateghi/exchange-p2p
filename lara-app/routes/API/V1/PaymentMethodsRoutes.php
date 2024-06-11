<?php

/*
|--------------------------------------------------------------------------
| User's Payment methods Routes
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\API\V1\PaymentMethods\User\DeletePaymentMethodController;
use App\Http\Controllers\API\V1\PaymentMethods\User\IndexPaymentMethodController;
use App\Http\Controllers\API\V1\PaymentMethods\User\ShowPaymentMethodController;
use App\Http\Controllers\API\V1\PaymentMethods\User\StorePaymentMethodController;
use App\Http\Controllers\API\V1\PaymentMethods\User\UpdatePaymentMethodController;

// todo-mn: add middleware for applicant
Route::middleware('auth:api')->name('users.payment-methods.')->prefix('users/payment-methods')->group(function () {
    Route::get('/',IndexPaymentMethodController::class)->name('index');
    Route::middleware('can:view,paymentMethod')->get('/{paymentMethod}',ShowPaymentMethodController::class)->name('show');
    Route::post('/',StorePaymentMethodController::class)->name('store');
    Route::middleware('can:update,paymentMethod')->put('/{paymentMethod}',UpdatePaymentMethodController::class)->name('update');
    Route::middleware('can:delete,paymentMethod')->delete('/{paymentMethod}',DeletePaymentMethodController::class)->name('delete');
});