<?php

use App\Http\Controllers\BidController;
use App\Http\Controllers\RequestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/* Request Routes */
Route::get('/requests', [RequestController::class,'index'])->name('requests.index');

/* Bid Routes */
Route::get('/bids/request/{requestId}', [BidController::class,'getBids'])->name('bids.request.getBids');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

