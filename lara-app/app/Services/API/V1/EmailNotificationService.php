<?php

namespace App\Services\API\V1;


use App\Notifications\SendVerificationCode;
use App\Notifications\VerificationCodeNotification;

class EmailNotificationService
{
    public function verificationCode($verificationCode, $code): void
    {
        $verificationCode->notify(new VerificationCodeNotification($code));
    }
}
