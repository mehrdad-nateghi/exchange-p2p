<?php

/*
|--------------------------------------------------------------------------
| Trade Routes For Amin
|--------------------------------------------------------------------------
*/


use App\Http\Controllers\API\V1\Trades\Admin\IndexTradeController;
use App\Http\Controllers\API\V1\Trades\Admin\ShowTradeController;

Route::middleware(['auth:sanctum', 'role:admin'])->name('admins.trades.')->prefix('admins/trades')->group(function () {
    Route::get('/', IndexTradeController::class)->name('index');
    Route::get('/{trade}', ShowTradeController::class)->name('show');

});
