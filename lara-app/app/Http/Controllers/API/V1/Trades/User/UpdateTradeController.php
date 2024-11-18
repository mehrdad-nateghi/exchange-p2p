<?php

namespace App\Http\Controllers\API\V1\Trades\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\Trade\User\UpdateTradeRequest;
use App\Http\Resources\TradeResource;
use App\Models\Trade;
use App\Services\API\V1\TradeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateTradeController extends Controller
{
    public function __invoke(
        UpdateTradeRequest $request,
        Trade            $trade,
        TradeService     $tradeService
    ): JsonResponse {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            //$data['deposit_reason_accepted'] = false;

            $tradeService->update($trade, $data);

            $resource = new TradeResource($trade->refresh());

            DB::commit();

            return apiResponse()
                ->message(trans('api-messages.update_success', ['attribute' => trans('api-messages.trade')]))
                ->data($resource)
                ->getApiResponse();
        } catch (\Throwable $t) {
            DB::rollBack();
            Log::error($t);
            return internalServerError();
        }
    }
}
