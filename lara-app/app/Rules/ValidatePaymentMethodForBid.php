<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\Rule;
use App\Models\Request;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Auth;

class ValidatePaymentMethodForBid implements Rule
{
    public function passes($attribute, $value)
    {
        $paymentMethod = PaymentMethod::where('ulid', $value)->first();

        if (!$paymentMethod || !$paymentMethod->paymentMethod->is_active || $paymentMethod->user_id != Auth::id()) {
            return false;
        }

        return true;
    }

    public function message()
    {
        return __('validation.exists', ['attribute' => __('api-messages.payment_method')]);
    }
}
