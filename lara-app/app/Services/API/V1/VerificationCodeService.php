<?php

namespace App\Services\API\V1;


use App\Data\VerificationCodeData;
use App\Models\VerificationCode;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;

class VerificationCodeService
{
    private VerificationCode $model;
    public string $code;

    public function __construct(VerificationCode $model)
    {
        $this->model = $model;
    }

    public function store(VerificationCodeData $data)
    {
        $this->setCode();
        $data->additional([
            'code' => $this->encryptCode()
        ]);
        return $this->model->create($data->toArray());
    }

    public function isValidCode(\App\Data\VerificationCodeData $data): bool
    {
        $verificationCode = $this->model->where('to', $data->to)
            ->where('via', $data->via)
            ->where('type', $data->type)
            ->latest()
            ->first();

        return !empty($verificationCode) &&
            $this->decryptCode($verificationCode->code) === $data->code &&
            Carbon::now()->lessThan($verificationCode->expired_at);
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

    private function decryptCode($code): string
    {
        return Crypt::decryptString($code);
    }
}
