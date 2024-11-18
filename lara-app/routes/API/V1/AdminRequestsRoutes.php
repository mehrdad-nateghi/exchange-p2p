<?php

use App\Http\Controllers\API\V1\Requests\Admin\IndexRequestController;
use App\Http\Controllers\API\V1\Requests\Admin\ShowRequestController;

Route::middleware(['auth:sanctum', 'role:admin'])->name('admins.requests.')->prefix('admins/requests')->group(function () {
    Route::get('/', IndexRequestController::class)->name('index');
    Route::get('/{request}', ShowRequestController::class)->name('show');
    //Route::patch('/{trade}/cancel', CancelTradeController::class)->name('trade.cancel');
});
