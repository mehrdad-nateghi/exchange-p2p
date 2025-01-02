<?php

namespace App\Rules;

use App\Enums\BidStatusEnum;
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
        $activeStatuses = [
            RequestStatusEnum::PENDING->value,
            RequestStatusEnum::PROCESSING->value,
            RequestStatusEnum::TRADING->value
        ];

        $requestExists = $this->user->requests()
            ->whereIn('status', $activeStatuses)
            ->whereHas('paymentMethods', fn($q) => $q->where('payment_methods.id', $this->paymentMethod->id))
            ->exists();

        $bidExists = $this->user->bids()
            ->whereIn('status', [
                BidStatusEnum::REGISTERED->value,
                BidStatusEnum::ACCEPTED->value
            ])
            ->whereHas('request', fn($q) => $q->whereIn('status', $activeStatuses))
            ->whereHas('paymentMethod', fn($q) => $q->where('payment_methods.id', $this->paymentMethod->id))
            ->exists();

        return !($requestExists || $bidExists);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return __('validation.payment_method_in_use');
    }
}
