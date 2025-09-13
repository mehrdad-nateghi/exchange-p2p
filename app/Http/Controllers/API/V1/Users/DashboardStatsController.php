<?php

namespace App\Http\Controllers\API\V1\Users;

use App\Http\Controllers\Controller;
use App\Http\Resources\RequestCollection;
use App\Http\Resources\TradeCollection;
use App\Models\Request;
use App\Models\Trade;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class DashboardStatsController extends Controller
{
    public function __invoke(): JsonResponse
    {
        try {
            $requests = Request::getOngoingForUser()->get();
            $trades = Trade::getOngoingForUser()->get();

            $data = [
                'ongoing_requests' => new RequestCollection($requests),
                'ongoing_trades' =>  new TradeCollection($trades)
            ];

            return apiResponse()
                ->message(trans('api-messages.retrieve_success', ['attribute' => trans('api-messages.user_stats')]))
                ->data($data)
                ->getApiResponse();
        } catch (\Throwable $t) {
            Log::error($t);
            return internalServerError();
        }
    }
}
