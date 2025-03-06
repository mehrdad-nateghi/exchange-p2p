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
use App\Services\ThirdParty\FinnoTech\FinnoTechService;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;

Route::name('global.')->group(function () {
    Route::get('/health', HealthCheckController::class)->name('health.check');
    Route::get('/me-test', MeTestController::class)->name('me.test');
    Route::get('/daily-rate-range', DailyRateRangeController::class)->name('daily.rate.range');
    Route::get('/gateway/callback', GatewayCallbackController::class)->name('gateway.callback');
    Route::get('/v1/banks-info', function (FinnoTechService  $finnoTechService) {
        $banksInfo = $finnoTechService->withClientCredentials()->getBanksInfo();
        return response()->json($banksInfo);
    })->name('banks-info');
    Route::get('/v1/test', function (FinnoTechService  $finnoTechService) {

//        $params = [
//            'username' => 'paylibero',
//            'password' => '2#y$qOU4!p87wInH',
//            'from' => '9982001523',
//            'to' => '09132424577',
//            'text' => 'Hi'
//        ];
//
//        $response = Http::timeout(10)
//            ->withoutVerifying()
//            ->retry(1, 3000)
//            ->post('https://rest.payamak-panel.com/api/SendSMS/SendSMS', $params);
//
//        $response_body = $response?->body();
//        $status_code = $response?->status();
//
//        dd($response_body, $status_code);


        $data = $finnoTechService->withAuthorizationCode();
        $cardToIban = $finnoTechService->withClientCredentials()->getCardToIban('5041721019784678');
        return response()->json(
            [
                'auth_token' => $data->authorizationToken,
                'credential_token' => $data->clientCredentialsToken,
                'cardToIban' => $cardToIban
            ]
        );
    })->name('test');
});
