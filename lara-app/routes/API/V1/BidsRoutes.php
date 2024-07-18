<?php

/*
|--------------------------------------------------------------------------
| User's Bids Routes
|--------------------------------------------------------------------------
*/


use App\Http\Controllers\API\V1\Bids\User\StoreBidController;

Route::middleware('auth:sanctum')->name('users.bids.')->prefix('users/bids')->group(function () {
    //Route::get('/', IndexRequestController::class)->name('index');
   // Route::middleware('can:view,request')->get('/{request}', ShowRequestController::class)->name('show');
    Route::post('/', StoreBidController::class)->name('store');
   // Route::middleware('can:update,paymentMethod')->put('/{paymentMethod}',UpdatePaymentMethodController::class)->name('update');
   // Route::middleware('can:delete,paymentMethod')->delete('/{paymentMethod}',DeletePaymentMethodController::class)->name('delete');
});
