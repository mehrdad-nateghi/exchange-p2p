<?php

use App\Http\Controllers\API\V1\File\User\ShowFileController;
use App\Http\Controllers\API\V1\File\User\UpdateReceiptController;
use App\Http\Controllers\API\V1\File\User\UploadReceiptController;

Route::middleware('auth:sanctum')->name('users.files.')->prefix('users/files')->group(function () {
    Route::middleware('can:upload,tradeStep')->post('/steps/{tradeStep}/upload', UploadReceiptController::class)->name('upload');
    Route::middleware('can:update,file')->patch('/{file}',UpdateReceiptController::class)->name('update');
    Route::middleware('can:view,file')->get('/{file}',ShowFileController::class)->name('show');
});
