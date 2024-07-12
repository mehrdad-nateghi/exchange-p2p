<?php

/*
|--------------------------------------------------------------------------
| User Routes
|--------------------------------------------------------------------------
*/


use App\Http\Controllers\API\V1\Requests\Guest\IndexRequestController;

Route::get('/requests', IndexRequestController::class)->name('requests.index');
