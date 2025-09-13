<?php

namespace App\Http\Controllers\API\V1\PaymentMethods\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\V1\PaymentMethod\User\IndexPaymentMethodRequest;
use App\Http\Resources\PaymentMethodCollection;
use App\QueryFilters\PaymentMethodTypeFilter;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class IndexPaymentMethodController extends Controller
{
    public function __invoke(
        IndexPaymentMethodRequest $request
    ): JsonResponse
    {
        try {
            $paymentMethods = QueryBuilder::for(Auth::user()->paymentMethods())
                ->allowedFilters([
                    AllowedFilter::custom('type', new PaymentMethodTypeFilter()),
                ])
                ->defaultSort(['-created_at'])
                ->allowedSorts('created_at')
                ->paginateWithDefault();

            $paymentMethods = new PaymentMethodCollection($paymentMethods);

            return apiResponse()
                ->message(trans('api-messages.retrieve_success', ['attribute' => trans('api-messages.payment_methods')]))
                ->data($paymentMethods)
                ->getApiResponse();
        } catch (\Throwable $t) {
            Log::error($t);
            return internalServerError();
        }
    }
}
