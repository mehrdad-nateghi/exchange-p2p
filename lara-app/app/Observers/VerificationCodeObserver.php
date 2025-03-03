<?php

namespace App\Observers;

use App\Enums\VerificationCodeViaEnum;
use App\Models\VerificationCode;
use Carbon\Carbon;

class VerificationCodeObserver
{
    /**
     * Handle the VerificationCode "created" event.
     *
     * @param  \App\Models\VerificationCode  $verificationCode
     * @return void
     */
    public function created(VerificationCode $verificationCode)
    {
        //
    }

    /**
     * @throws \Exception
     */
    public function creating(VerificationCode $verificationCode): void
    {
        $configKey = $verificationCode->via->value === VerificationCodeViaEnum::EMAIL->value
            ? 'constants.email_verification_code_expiration_time_per_minutes'
            : 'constants.mobile_verification_code_expiration_time_per_minutes';

        $verificationCode->expired_at = Carbon::now()->addMinutes(config($configKey));
    }

    /**
     * Handle the VerificationCode "updated" event.
     *
     * @param  \App\Models\VerificationCode  $verificationCode
     * @return void
     */
    public function updated(VerificationCode $verificationCode)
    {
        //
    }

    /**
     * Handle the VerificationCode "deleted" event.
     *
     * @param  \App\Models\VerificationCode  $verificationCode
     * @return void
     */
    public function deleted(VerificationCode $verificationCode)
    {
        //
    }

    /**
     * Handle the VerificationCode "restored" event.
     *
     * @param  \App\Models\VerificationCode  $verificationCode
     * @return void
     */
    public function restored(VerificationCode $verificationCode)
    {
        //
    }

    /**
     * Handle the VerificationCode "force deleted" event.
     *
     * @param  \App\Models\VerificationCode  $verificationCode
     * @return void
     */
    public function forceDeleted(VerificationCode $verificationCode)
    {
        //
    }
}
