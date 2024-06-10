<?php

/*
|--------------------------------------------------------------------------
| Global Routes
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\API\V1\Public\HealthCheckController;

Route::name('global.')->group(function () {
    Route::get('/health',HealthCheckController::class)->name('health.check');
    Route::get('/temp',function (){
        return response()->json([
            'message' => "Are you kidding me?"
        ]);
    })->name('health.temp');
});
