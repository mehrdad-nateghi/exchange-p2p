<?php

namespace App\Http\Controllers\API\V1\DepositReason\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\DepositReason\Admin\IndexDepositReasonRequest;
use App\Http\Resources\DepositReasons\Admin\DepositReasonCollection;
use App\Models\DepositReason;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class IndexDepositReasonController extends Controller
{
    public function __invoke(): JsonResponse
    {
        try {
            $depositReasons = new DepositReasonCollection(DepositReason::all());

            return apiResponse()
                ->message(trans('api-messages.retrieve_success', ['attribute' => trans('api-messages.deposit_reason')]))
                ->data($depositReasons)
                ->getApiResponse();
        } catch (\Throwable $t) {
            Log::error($t);
            return internalServerError();
        }
    }
}
