<?php

/*
|--------------------------------------------------------------------------
| User's Bids Routes
|--------------------------------------------------------------------------
*/


use App\Http\Controllers\API\V1\ThirdParty\FinnoTech\User\CardToIbanController;
use App\Http\Controllers\API\V1\ThirdParty\FinnoTech\User\VerifyCardNumberOwnershipController;
use App\Http\Controllers\API\V1\ThirdParty\FinnoTech\User\VerifyMobileOwnershipController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

Route::middleware('auth:sanctum')->name('users.third-party.finnotech')->prefix('users/third-party/finnotech')->group(function () {
    Route::get('/card-info', CardToIbanController::class)->name('card.info');
    Route::get('/verify-mobile-ownership', VerifyMobileOwnershipController::class)->name('verify.mobile.ownership');
    Route::get('/verify-card-number-ownership', VerifyCardNumberOwnershipController::class)->name('verify.card.number.ownership');
});
