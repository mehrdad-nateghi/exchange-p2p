<?php

namespace App\Services\API\V1;


use App\Data\VerificationCodeData;
use App\Models\VerificationCode;
use Carbon\Carbon;
use Illuminate\Support\Facades\Crypt;

class VerificationCodeService
{
    private VerificationCode $model;

    public function __construct(VerificationCode $model)
    {
        $this->model = $model;
    }

    public function store(array $data)
    {
        return $this->model->create($data);
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

    public function generateCode(): int
    {
         return random_int(100000, 999999);
    }

    public function encryptCode($code): string
    {
        return Crypt::encryptString($code);
    }

    public function decryptCode($code): string
    {
        return Crypt::decryptString($code);
    }
}
