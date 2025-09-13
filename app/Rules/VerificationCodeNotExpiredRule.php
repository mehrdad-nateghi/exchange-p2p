<?php

namespace App\Rules;

use App\Models\VerificationCode;
use Illuminate\Contracts\Validation\Rule;

class VerificationCodeNotExpiredRule implements Rule
{
    private int $via;
    private int $type;

    public function __construct($via, $type)
    {
        $this->via = $via;
        $this->type = $type;
    }
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute,$value): bool
    {
        $verificationCode = VerificationCode::query()
            ->where('to',$value)
            ->where('via',$this->via)
            ->where('type',$this->type)
            ->where('expired_at','>=',now())
            ->latest()
            ->first();
        return empty($verificationCode);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return __('validation.verification_code_not_expired');
    }
}
