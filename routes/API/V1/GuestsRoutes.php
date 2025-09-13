<?php

/*
|--------------------------------------------------------------------------
| User Routes
|--------------------------------------------------------------------------
*/


use App\Http\Controllers\API\V1\Bids\Guest\IndexBidsOfRequestController;
use App\Http\Controllers\API\V1\Requests\Guest\IndexRequestController;
use App\Http\Controllers\API\V1\Requests\Guest\ShowRequestController;
use App\Http\Controllers\API\V1\Trades\Guest\IndexTradeController;

Route::get('/requests', IndexRequestController::class)->name('requests.index');
Route::get('/requests/{request}/bids', IndexBidsOfRequestController::class)->name('requests.bids.index');
Route::get('/requests/{request}', ShowRequestController::class)->name('requests.show');
Route::get('/trades', IndexTradeController::class)->name('trades.index');

