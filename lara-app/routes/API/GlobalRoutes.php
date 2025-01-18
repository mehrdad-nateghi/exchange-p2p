<?php

/*
|--------------------------------------------------------------------------
| Global Routes
|--------------------------------------------------------------------------
*/

use App\Enums\FileStatusEnum;
use App\Events\PayTomanToSystemEvent;
use App\Events\TransferToSellerEvent;
use App\Events\UpdateReceiptByBuyerEvent;
use App\Events\UploadReceiptEvent;
use App\Http\Controllers\API\V1\Public\DailyRateRangeController;
use App\Http\Controllers\API\V1\Public\GatewayCallbackController;
use App\Http\Controllers\API\V1\Public\HealthCheckController;
use App\Http\Controllers\API\V1\Public\MeTestController;
use App\Http\Resources\Notifications\User\NotificationCollection;
use App\Models\Invoice;
use App\Models\Trade;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\Notification;

Route::name('global.')->group(function () {
    Route::get('/health',HealthCheckController::class)->name('health.check');
    Route::get('/me-test',MeTestController::class)->name('me.test');
    Route::get('/daily-rate-range',DailyRateRangeController::class)->name('daily.rate.range');
    Route::get('/gateway/callback',GatewayCallbackController::class)->name('gateway.callback');

    Route::get('/v1/test',function (){
        //$trade = Trade::find(1);
        $invoice = Invoice::find(1);

        event(new TransferToSellerEvent($invoice->refresh()));

//        event(new UpdateReceiptByBuyerEvent($trade->refresh(), FileStatusEnum::ACCEPT_BY_BUYER->value));
//        event(new UpdateReceiptByBuyerEvent($trade->refresh(), FileStatusEnum::REJECT_BY_BUYER->value));


        //event(new UploadReceiptEvent($trade->refresh()));


        $notifications = DatabaseNotification::query()->get();

        $notifications = new NotificationCollection($notifications);

        return apiResponse()
            ->message(trans('api-messages.retrieve_success', ['attribute' => trans('api-messages.notifications')]))
            ->data($notifications)
            ->getApiResponse();

        $invoice = Invoice::find(1);
        $trade = $invoice->invoiceable;

        event(new PayTomanToSystemEvent($trade->refresh(), $invoice->refresh()));

    })->name('test');
});
