<?php

/*
|--------------------------------------------------------------------------
| User's Request Routes
|--------------------------------------------------------------------------
*/


// todo-mn: add middleware for applicant
use App\Http\Controllers\API\V1\Requests\User\IndexRequestController;
use App\Http\Controllers\API\V1\Requests\User\ShowRequestController;
use App\Http\Controllers\API\V1\Requests\User\StoreRequestController;
use App\Http\Controllers\API\V1\Trades\User\IndexTradeController;

Route::middleware('auth:sanctum')->name('users.trades.')->prefix('users/trades')->group(function () {
    Route::get('/', IndexTradeController::class)->name('index');
    //Route::middleware('can:view,request')->get('/{request}', ShowRequestController::class)->name('show');
   // Route::post('/', StoreRequestController::class)->name('store');
   // Route::middleware('can:update,paymentMethod')->put('/{paymentMethod}',UpdatePaymentMethodController::class)->name('update');
   // Route::middleware('can:delete,paymentMethod')->delete('/{paymentMethod}',DeletePaymentMethodController::class)->name('delete');
});
