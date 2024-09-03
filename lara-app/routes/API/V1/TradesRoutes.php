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
use App\Http\Controllers\API\V1\Trades\User\ShowTradeController;
use App\Http\Controllers\API\V1\Trades\User\UpdateTradeController;

Route::middleware('auth:sanctum')->name('users.trades.')->prefix('users/trades')->group(function () {
    Route::get('/', IndexTradeController::class)->name('index');
    Route::middleware('can:view,trade')->get('/{trade}', ShowTradeController::class)->name('show');
    Route::middleware('can:update,trade')->put('/{trade}',UpdateTradeController::class)->name('update');
});
