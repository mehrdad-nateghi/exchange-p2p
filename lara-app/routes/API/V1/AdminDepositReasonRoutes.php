<?php

/*
|--------------------------------------------------------------------------
| Trade Routes For Amin
|--------------------------------------------------------------------------
*/


use App\Http\Controllers\API\V1\DepositReason\Admin\IndexDepositReasonController;

Route::middleware(['auth:sanctum', 'role:admin'])->name('admins.deposit-reasons.')->prefix('admins/deposit-reasons')->group(function () {
    Route::get('/', IndexDepositReasonController::class)->name('index');
});
