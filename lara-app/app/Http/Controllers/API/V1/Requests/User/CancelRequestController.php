<?php

namespace App\Http\Controllers\API\V1\Requests\User;

use App\Enums\BidStatusEnum;
use App\Enums\RequestStatusEnum;
use App\Enums\TradeStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Request;
use App\Services\API\V1\BidService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CancelRequestController extends Controller
{
    public function __invoke(
        Request $request,
        BidService $bidService,
    ): JsonResponse {
        try {
            DB::beginTransaction();

            if($request->trades()->exists()){
                $request->trades()->update([
                    'status' => TradeStatusEnum::CANCELED->value,
                    'canceled_at' => now(),
                ]);
            }

            $request->update([
                'status' => RequestStatusEnum::CANCELED->value,
                'canceled_at' => now(),
            ]);

            if($request->bids()->exists()){
                $request->bids()
                    ->where('status', '!=', BidStatusEnum::ACCEPTED->value)
                    ->update([
                        'status' => BidStatusEnum::REJECTED,
                        'rejected_at' => now(),
                    ]);
            }

            DB::commit();

            return apiResponse()
                ->message(trans('api-messages.delete_success', ['attribute' => trans('api-messages.request')]))
                ->getApiResponse();
        } catch (\Throwable $t) {
            DB::rollBack();
            Log::error($t);
            return internalServerError();
        }
    }
}
