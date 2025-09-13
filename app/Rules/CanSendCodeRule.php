<?php

namespace App\Rules;

use App\Models\VerificationCode;
use Illuminate\Contracts\Validation\Rule;
use Carbon\Carbon;

class CanSendCodeRule implements Rule
{
    private int $via;
    private int $type;
    private int $cooldownMinutes;

    public function __construct($via, $type, $cooldownMinutes = null)
    {
        $this->via = $via;
        $this->type = $type;
        $this->cooldownMinutes = $cooldownMinutes ?? config('constants.verification_code_cooldown_minutes');
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
        $latestCode = VerificationCode::query()
            ->where('to', $value)
            ->where('via', $this->via)
            ->where('type', $this->type)
            ->latest('created_at')
            ->first();

        // If no code exists, allow sending
        if (empty($latestCode)) {
            return true;
        }

        // Check if the cooldown period has passed since the latest code was created
        $cooldownEndsAt = Carbon::parse($latestCode->created_at)->addMinutes($this->cooldownMinutes);
        return now()->greaterThanOrEqualTo($cooldownEndsAt);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return __('validation.can_send_code', ['minutes' => $this->cooldownMinutes]);
    }
} 