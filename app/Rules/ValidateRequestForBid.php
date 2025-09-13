<?php

namespace App\Rules;

use App\Enums\RequestStatusEnum;
use Illuminate\Contracts\Validation\Rule;
use App\Models\Request;

class ValidateRequestForBid implements Rule
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        return !empty($this->request) && in_array($this->request->status->value, [RequestStatusEnum::PENDING->value,RequestStatusEnum::PROCESSING->value]);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return __('validation.exists', ['attribute' => __('api-messages.request')]);
    }
}
