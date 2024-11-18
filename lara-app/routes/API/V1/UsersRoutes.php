<?php

/*
|--------------------------------------------------------------------------
| User Routes
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\API\V1\Users\MeController;

Route::middleware('auth:sanctum')->name('users.')->prefix('users')->group(function () {
    Route::get('/me',MeController::class)->name('me');
});
