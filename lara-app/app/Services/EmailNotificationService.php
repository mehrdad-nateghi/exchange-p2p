<?php

namespace App\Services;


use App\Mail\EmailVerificationMail;
use App\Models\VerificationCode;
use App\Notifications\SendVerificationCode;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailNotificationService
{
    public function sendVerificationCode($verificationCode, $code): void
    {
        $verificationCode->notify(new SendVerificationCode($code));
    }
}
