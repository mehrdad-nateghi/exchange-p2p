<?php

/*
|--------------------------------------------------------------------------
| User Routes
|--------------------------------------------------------------------------
*/


use App\Http\Controllers\API\V1\Bids\Guest\IndexBidsOfRequestController;
use App\Http\Controllers\API\V1\Requests\Guest\IndexRequestController;

Route::get('/requests', IndexRequestController::class)->name('requests.index');
Route::get('/requests/{request}/bids', IndexBidsOfRequestController::class)->name('requests.bids.index');
