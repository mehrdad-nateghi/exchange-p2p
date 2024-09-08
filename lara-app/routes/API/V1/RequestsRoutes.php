<?php

/*
|--------------------------------------------------------------------------
| User's Request Routes
|--------------------------------------------------------------------------
*/


// todo-mn: add middleware for applicant
use App\Http\Controllers\API\V1\Requests\User\DeleteRequestController;
use App\Http\Controllers\API\V1\Requests\User\IndexRequestController;
use App\Http\Controllers\API\V1\Requests\User\ShowRequestController;
use App\Http\Controllers\API\V1\Requests\User\StoreRequestController;

Route::middleware('auth:sanctum')->name('users.requests.')->prefix('users/requests')->group(function () {
    Route::get('/', IndexRequestController::class)->name('index');
    Route::middleware('can:view,request')->get('/{request}', ShowRequestController::class)->name('show');
    Route::post('/', StoreRequestController::class)->name('store');
    Route::middleware('can:delete,request')->delete('/{request}',DeleteRequestController::class)->name('delete');
});
