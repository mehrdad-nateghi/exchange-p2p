<?php

namespace App\Http\Controllers\API\V1\Requests\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\PaymentMethodCollection;
use App\Http\Resources\RequestCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class IndexRequestController extends Controller
{
    public function __invoke(
    ): JsonResponse
    {
        try {
            $user = Auth::user();
            $requests = new RequestCollection($user->requests()->paginate());

            return apiResponse()
                ->message(trans('api-messages.retrieve_success', ['attribute' => trans('api-messages.requests')]))
                ->data($requests)
                ->getApiResponse();
        } catch (\Throwable $t) {
            Log::error($t);
            return internalServerError();
        }
    }
}
