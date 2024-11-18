<?php

namespace App\Observers;

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
        //$verificationCode->code = generateVerificationCode();
        $verificationCode->expired_at = Carbon::now()->addMinutes(config('constants.verification_code_expiration_time_per_minutes'));
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
