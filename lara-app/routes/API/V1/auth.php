<?php

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/


use App\Http\Controllers\API\V1\Auth\Signup\SendCode\SendCodeController;
use App\Http\Controllers\API\V1\Auth\Signup\SignupController;

Route::name('auth.')->prefix('auth')->group(function () {
    Route::post('/signup',SignupController::class)->name('signup');
    //Route::get('/signup/send-code',SendCodeController::class)->name('signup.send-code');
});

///*Route:: group(['prefix' => 'auth'],function () {
//    Route::get('/signup/send-code',[SendCodeController::class])->name('');
//    // /captcha
//    // /verify-code
//    // /set-password
//    /* Route::post('/login',[// Your login controller or closure]);
//         Route::post('/logout',[// Your logout controller or closure]);
//
//             // Password Reset Routes
//             Route::get('/password/reset',[// Your forgot password form controller or closure]);
//                 Route::post('/password/reset',[// Your password reset controller or closure]);*/
//})->as;*/
