<?php

/*
|--------------------------------------------------------------------------
| User's Request Routes
|--------------------------------------------------------------------------
*/


// todo-mn: add middleware for applicant
use App\Http\Controllers\API\V1\Requests\User\StoreRequestController;

Route::middleware('auth:sanctum')->name('users.requests.')->prefix('users/requests')->group(function () {
    //Route::get('/',IndexPaymentMethodController::class)->name('index');
    //Route::middleware('can:view,paymentMethod')->get('/{paymentMethod}',ShowPaymentMethodController::class)->name('show');
    Route::post('/', StoreRequestController::class)->name('store');
   // Route::middleware('can:update,paymentMethod')->put('/{paymentMethod}',UpdatePaymentMethodController::class)->name('update');
   // Route::middleware('can:delete,paymentMethod')->delete('/{paymentMethod}',DeletePaymentMethodController::class)->name('delete');
});
