<?php

use App\Http\Controllers\API\V1\File\Admin\ShowFileController;
use App\Http\Controllers\API\V1\File\Admin\UpdateFileStatusAcceptController;

Route::middleware(['auth:sanctum', 'role:admin'])->name('admins.files.')->prefix('admins/files')->group(function () {
    Route::get('/{file}', ShowFileController::class)->name('show');
    Route::patch('/{file}/accept', UpdateFileStatusAcceptController::class)->name('accept');

});
