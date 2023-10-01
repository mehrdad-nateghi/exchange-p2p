<?php

use App\Http\Controllers\BidController;
use App\Http\Controllers\FinancialController;
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

/* Requests Management Routes */
Route::get('/requests/filter/{count?}', [RequestController::class,'getAllRequestsByFilter'])->name('requests.getByFilter');
Route::get('/requests/applicant/{applicantId}', [RequestController::class,'getApplicantAllRequests'])->name('requests.getApplicantAllRequests');
Route::get('/requests/applicant/{applicantId}/{requestId}', [RequestController::class,'getApplicantRequest'])->name('requests.getApplicantSpecificRequest');


Route::get('/requests/create/setup/{countryId}', [RequestController::class,'getRequestCreationInitialInformation'])->name('requests.create.setup');
Route::post('/requests/create', [RequestController::class,'create'])->name('requests.create');
Route::get('/requests/{requestId}', [RequestController::class,'getSpecificRequest'])->name('requests.get.single');

Route::get('/requests/edit/setup/{applicantId}/{requestId}', [RequestController::class,'getRequestUpdateInitialInformation'])->name('requests.edit.setup');

Route::get('/requests/edit/setup/{applicantId}/{requestId}', [RequestController::class,'getRequestUpdateInitialInformation'])->name('requests.edit.setup');

Route::put('requests/update/{applicantId}/{requestId}', [RequestController::class, 'update'])->name('requests.update');

/* Bid Management Routes */
Route::get('/bids/request/{requestId}', [BidController::class,'getBids'])->name('bids.request.getBids');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
