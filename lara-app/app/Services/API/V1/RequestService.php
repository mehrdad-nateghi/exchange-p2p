<?php

namespace App\Services\API\V1;

use App\Models\PaymentMethod;
use App\Models\Request;

class RequestService
{
    private Request $model;

    public function __construct(Request $model)
    {
        $this->model = $model;
    }

    public function create($user,$data)
    {
        return $user->requests()->create($data);
    }

    public function attachPaymentMethod($request,$paymentMethods)
    {
        $paymentMethodIds = PaymentMethod::whereIn('ulid', $paymentMethods)->pluck('id');
        return $request->paymentMethods()->attach($paymentMethodIds);
    }
}
