<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Guest\RequestController as GuestRequestController;
use App\Http\Controllers\Applicant\RequestController as ApplicantRequestController;
use App\Http\Controllers\Admin\RequestController as AdminRequestController;
use App\Http\Controllers\Applicant\AuthController as ApplicantAuthController;
use App\Http\Controllers\BidController;
use App\Http\Controllers\FinancialController;
use App\Http\Controllers\Guest\BidController as GuestBidController;
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
// Guest User Routes
Route::get('/requests/filter', [GuestRequestController::class,'getRequests'])->name('guest.requests.get.byFilter');
Route::get('/requests/{requestId}', [GuestRequestController::class,'getRequest'])->name('guest.requests.get.single');
// Applicant Routes
Route::get('/applicant/requests/{applicantId}', [ApplicantRequestController::class,'getAllRequests'])->name('applicant.requests.get.all');
Route::get('/applicant/requests/{applicantId}/{requestId}', [ApplicantRequestController::class,'getRequest'])->name('applicant.requests.get.single');
Route::get('/applicant/requests/create/setup/{countryId}', [ApplicantRequestController::class,'getRequestCreationInitialInformation'])->name('applicant.requests.create.setup');
Route::post('/applicant/requests/create', [ApplicantRequestController::class,'create'])->name('applicant.requests.create');
Route::get('/applicant/requests/update/setup/{applicantId}/{requestId}', [ApplicantRequestController::class,'getRequestUpdateInitialInformation'])->name('applicant.requests.edit.setup');
Route::put('/applicant/requests/update/{applicantId}/{requestId}', [ApplicantRequestController::class, 'update'])->name('applicant.requests.update');

Route::post('/applicant/signin',[ApplicantAuthController::class, 'signIn'])->name('applicant.auth.signin');
Route::middleware('auth:api')->group(function () {
    Route::post('/applicant/signout',[ApplicantAuthController::class, 'signout'])->name('applicant.auth.signout');
});
// Admin Routes
Route::post('/admin/signin',[AdminAuthController::class, 'signin'])->name('admin.auth.signin');
Route::middleware('auth:api')->group(function () {
    Route::post('/admin/signout',[AdminAuthController::class, 'signout'])->name('admin.auth.signout');
});
/* Bids Management Routes */
// Guest User Routes
Route::get('/bids/request/{requestId}', [GuestBidController::class,'getBids'])->name('request.bids.get.all');




Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
