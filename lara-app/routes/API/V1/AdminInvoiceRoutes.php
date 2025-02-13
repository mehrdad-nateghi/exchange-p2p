<?php

use App\Http\Controllers\API\V1\Invoice\Admin\StoreTransactionController;
use App\Http\Controllers\API\V1\Invoice\Admin\TransferToSellerController;

Route::middleware(['auth:sanctum', 'role:admin'])->name('admins.invoices.')->prefix('admins/invoices')->group(function () {
    Route::post('/{invoice}/transfer-to-seller', TransferToSellerController::class)->name('transfer.to.seller');
    Route::post('/{invoice}/transactions', StoreTransactionController::class)->name('transactions.store');
});
