<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\Rule;
use App\Models\Request;
use App\Models\PaymentMethod;

class ValidatePaymentMethodForBid implements Rule
{
    protected $requestUlid;

    public function __construct($requestUlid)
    {
        $this->requestUlid = $requestUlid;
    }

    public function passes($attribute, $value)
    {
        $request = Request::where('ulid', $this->requestUlid)->first();

        if (!$request) {
            return false;
        }

        $paymentMethod = PaymentMethod::where('ulid', $value)->first();

        if (!$paymentMethod || !$paymentMethod->paymentMethod->is_active) {
            return false;
        }

        return $request->paymentMethods->contains($paymentMethod);
    }

    public function message()
    {
        return __('validation.exists', ['attribute' => __('api-messages.payment_method')]);
    }
}
