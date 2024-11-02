<?php

namespace App\Rules;

use App\Enums\RequestStatusEnum;
use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Contracts\Validation\Rule;

class PaymentMethodIsInUse implements Rule
{
    private PaymentMethod $paymentMethod;
    protected $message;
    private User $user;

    public function __construct($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;
        $this->user = $paymentMethod->user;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $requestExists = $this->user->requests()
            ->whereIn('status', [
                RequestStatusEnum::PENDING->value,
                RequestStatusEnum::PROCESSING->value,
                RequestStatusEnum::TRADING->value
            ])
            ->where(function ($query) {
                $query->whereHas('paymentMethods', function ($q) {
                    $q->where('payment_methods.id', $this->paymentMethod->id);
                })->orWhereHas('bids', function ($q) {
                    $q->where('bids.payment_method_id', $this->paymentMethod->id);
                });
            })
            ->exists();

        if ($requestExists) {
            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return "This payment method cannot be modified or deleted while it's involved in active request.";
    }
}
