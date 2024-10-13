<?php

/*
|--------------------------------------------------------------------------
| Trade Routes For Amin
|--------------------------------------------------------------------------
*/


use App\Http\Controllers\API\V1\Requests\Admin\IndexRequestController;

Route::middleware(['auth:sanctum', 'role:admin'])->name('admins.requests.')->prefix('admins/requests')->group(function () {
    Route::get('/', IndexRequestController::class)->name('index');
    //Route::get('/{trade}', ShowTradeController::class)->name('show');
    //Route::patch('/{trade}/cancel', CancelTradeController::class)->name('trade.cancel');
});
