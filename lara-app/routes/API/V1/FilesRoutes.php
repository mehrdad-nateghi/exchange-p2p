<?php

/*
|--------------------------------------------------------------------------
| User's Request Routes
|--------------------------------------------------------------------------
*/


// todo-mn: add middleware for applicant
use App\Http\Controllers\API\V1\File\User\UploadReceiptController;

Route::middleware('auth:sanctum')->name('users.files.')->prefix('users/files')->group(function () {
    /*Route::get('/', IndexRequestController::class)->name('index');
    Route::middleware('can:view,request')->get('/{request}', ShowRequestController::class)->name('show');*/
    Route::post('/steps/{step}/upload', UploadReceiptController::class)->name('upload');
    //Route::middleware('can:update,request')->put('/{request}',UpdateRequestController::class)->name('update');
   // Route::middleware('can:delete,paymentMethod')->delete('/{paymentMethod}',DeletePaymentMethodController::class)->name('delete');
});
