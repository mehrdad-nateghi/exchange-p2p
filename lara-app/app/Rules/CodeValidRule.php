<?php

namespace App\Rules;

use App\Models\VerificationCode;
use App\Services\API\V1\VerificationCodeService;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Crypt;

class CodeValidRule implements Rule
{
    public string $to;
    private int $via;
    private int $type;

    public function __construct($to,$via,$type)
    {
        $this->to = $to;
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
        $code = $value;

        $verificationCode = VerificationCode::where('to',$this->to)
            ->where('via',$this->via)
            ->where('type',$this->type)
            ->latest()
            ->first();

        return !empty($verificationCode) &&
            Crypt::decryptString($verificationCode->code) === $code &&
            Carbon::now()->lessThan($verificationCode->expired_at);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return trans('api-messages.invalid_verification_code');
    }
}
