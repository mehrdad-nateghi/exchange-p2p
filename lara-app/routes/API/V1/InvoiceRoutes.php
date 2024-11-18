<?php

/*
|--------------------------------------------------------------------------
| Invoice Routes
|--------------------------------------------------------------------------
*/


use App\Http\Controllers\API\V1\Invoice\User\PayInvoiceController;

Route::middleware('auth:sanctum')->name('users.invoices.')->prefix('users/invoices')->group(function () {
    //Route::get('/', IndexRequestController::class)->name('index');
    //Route::middleware('can:view,request')->get('/{request}', ShowRequestController::class)->name('show');
    Route::middleware('can:create,invoice')->get('/{invoice}/pay', PayInvoiceController::class)->name('pay');
    //Route::middleware('can:update,request')->put('/{request}',UpdateTradeController::class)->name('update');
    // Route::middleware('can:delete,paymentMethod')->delete('/{paymentMethod}',DeletePaymentMethodController::class)->name('delete');
});
