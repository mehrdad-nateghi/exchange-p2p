<?php

/*
|--------------------------------------------------------------------------
| User's Bids Routes
|--------------------------------------------------------------------------
*/


use App\Http\Controllers\API\V1\Bids\User\IndexBidController;
use App\Http\Controllers\API\V1\Bids\User\StoreBidController;
use App\Http\Controllers\API\V1\Bids\User\AcceptBidController;
use App\Models\Bid;

Route::middleware('auth:sanctum')->name('users.bids.')->prefix('users/bids')->group(function () {
    Route::get('/', IndexBidController::class)->name('index');
   // Route::middleware('can:view,request')->get('/{request}', ShowRequestController::class)->name('show');
    Route::post('/', StoreBidController::class)->name('store');
    Route::middleware('can:update,bid')->patch('/{bid}',AcceptBidController::class)->name('update');
   // Route::middleware('can:delete,paymentMethod')->delete('/{paymentMethod}',DeletePaymentMethodController::class)->name('delete');
});
