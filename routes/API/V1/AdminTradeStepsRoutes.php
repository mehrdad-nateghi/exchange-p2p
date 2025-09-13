<?php

use App\Http\Controllers\API\V1\TradeSteps\Admin\IncreaseExpireAtTradeStepController;

Route::middleware(['auth:sanctum', 'role:admin'])->name('admins.trade-steps.')->prefix('admins/trade-steps')->group(function () {
    Route::patch('/{tradeStep}/increase-expire-at', IncreaseExpireAtTradeStepController::class)->name('increase-expire-at');
});
