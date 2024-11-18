<?php

namespace App\Http\Controllers\API\V1\Requests\User;

use App\Enums\RequestStatusEnum;
use App\Enums\TradeStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DeleteRequestController extends Controller
{
    public function __invoke(
        Request $request,
    ): JsonResponse {
        try {
            DB::beginTransaction();

            // Soft delete associated trades
            $request->trades()->update([
                'status' => TradeStatusEnum::CANCELED->value,
                'canceled_at' => now(),
            ]);
            $request->trades()->delete();

            // Soft delete the request
            $request->update([
                'status' => RequestStatusEnum::CANCELED->value,
            ]);
            $request->delete();

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
