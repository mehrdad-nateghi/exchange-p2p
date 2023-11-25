<?php

use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Guest\RequestController as GuestRequestController;
use App\Http\Controllers\Applicant\RequestController as ApplicantRequestController;
use App\Http\Controllers\Admin\RequestController as AdminRequestController;
use App\Http\Controllers\Applicant\AuthController as ApplicantAuthController;
use App\Http\Controllers\Guest\BidController as GuestBidController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Applicant\PaymentMethodController as ApplicantPaymentMethodController;
use App\Http\Controllers\Admin\PaymentMethodController as AdminPaymentMethodController;

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
Route::post('/applicant/signin',[ApplicantAuthController::class, 'signIn'])->name('applicant.auth.signin');
Route::middleware(['auth:api', 'is.applicant'])->prefix('applicant')->group(function () {
    Route::get('/requests', [ApplicantRequestController::class,'getOwnAllRequests'])->name('applicant.requests.get.all');
    Route::get('/requests/{requestId}', [ApplicantRequestController::class,'getOwnRequest'])->name('applicant.requests.get.single');
    Route::get('/requests/create/setup/{countryId}', [ApplicantRequestController::class,'getSetupInformationForRequestCreation'])->name('applicant.requests.create.setup');
    Route::post('/requests/create', [ApplicantRequestController::class,'create'])->name('applicant.requests.create');
    Route::get('/requests/update/setup/{requestId}', [ApplicantRequestController::class,'getSetupInformationForRequestUpdate'])->name('applicant.requests.update.setup');
    Route::put('/requests/update/{requestId}', [ApplicantRequestController::class, 'update'])->name('applicant.requests.update');
    Route::delete('/requests/remove/{requestId}', [ApplicantRequestController::class, 'remove'])->name('applicant.requests.remove');
    Route::post('/signout',[ApplicantAuthController::class, 'signout'])->name('applicant.auth.signout');
    Route::post('/payment-methods/link/{paymentMethodId}', [ApplicantPaymentMethodController::class, 'linkPaymentMethod'])->name('applicant.paymentMethods.link');
    Route::get('/payment-methods', [ApplicantPaymentMEthodController::class, 'getPaymentMethods'])->name('applicant.paymentMethods.get');
    Route::delete('/payment-methods/unlink/{linkedMethodId}', [ApplicantPaymentMethodController::class, 'unlinkPaymentMethod'])->name('applicant.paymentMethods.unlink');
    Route::put('/payment-methods/linked-method/update/{linkedMethodId}', [ApplicantPaymentMethodController::class, 'updateLinkedMethod'])->name('applicant.paymentMethods.linkedMethod.update');

});

// Admin Routes
Route::post('/admin/signin',[AdminAuthController::class, 'signin'])->name('admin.auth.signin');
Route::middleware(['auth:api', 'is.admin'])->prefix('admin')->prefix('admin')->group(function () {
    Route::delete('/requests/remove/{requestId}', [AdminRequestController::class, 'remove'])->name('admin.requests.remove');
    Route::get('/requests/update/setup/{requestId}', [AdminRequestController::class,'getSetupInformationForRequestUpdate'])->name('admin.requests.update.setup');
    Route::put('/requests/update/{requestId}', [AdminRequestController::class, 'update'])->name('admin.requests.update');
    Route::post('/signout',[AdminAuthController::class, 'signout'])->name('admin.auth.signout');
    Route::get('/applicant/payment-methods/{applicantId}', [AdminPaymentMethodController::class, 'getApplicantPaymentMethods'])->name('admin.applicantPaymentMethods.get');
    Route::delete('/payment-methods/unlink/{linkedMethodId}', [AdminPaymentMethodController::class, 'unlinkPaymentMethod'])->name('admin.paymentMethods.unlink');
    Route::post('/applicant/payment-methods/link/{applicantId}/{paymentMethodId}', [AdminPaymentMethodController::class, 'linkPaymentMethodToApplicantAccount'])->name('admin.applicant.paymentMethods.link');
});

/* Bids Management Routes */
// Guest User Routes
Route::get('/bids/request/{requestId}', [GuestBidController::class,'getBids'])->name('request.bids.get.all');




Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
