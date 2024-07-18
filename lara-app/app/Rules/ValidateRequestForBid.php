<?php

namespace App\Rules;

use App\Enums\RequestStatusEnum;
use Illuminate\Contracts\Validation\Rule;
use App\Models\Request;

class ValidateRequestForBid implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        $request = Request::where('ulid', $value)->first();
        return $request && in_array($request->status->value, [RequestStatusEnum::PENDING->value,RequestStatusEnum::PROCESSING->value]);
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
