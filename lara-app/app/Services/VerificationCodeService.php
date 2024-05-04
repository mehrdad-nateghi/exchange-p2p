<?php

namespace App\Services;


use App\Mail\EmailVerificationMail;
use App\Models\VerificationCode;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class VerificationCodeService
{
    private VerificationCode $model;
    public string $code;

    public function __construct(VerificationCode $model)
    {
        $this->model = $model;
    }

    public function store(array $data)
    {
        $this->setCode();
        $data['code'] = $this->encryptCode();
        return $this->model->create($data);
    }

    private function setCode(): void
    {
         $this->code = random_int(100000, 999999);
    }

    public function getCode(): string
    {
        return $this->code;
    }

    private function encryptCode(): string
    {
        return Crypt::encryptString($this->code);
    }

/*    public function sendCodeViaEmail(string $email, string $code): bool
    {
        $code = Crypt::decryptString($code);
        Mail::to($email)->send(new EmailVerificationMail($code));
        return true;
    }*/
}
