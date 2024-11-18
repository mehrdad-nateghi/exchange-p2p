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

    public function findLatest(string $to,string $via,string $type)
    {
        return $this->model->where('to',$to)
            ->where('via',$via)
            ->where('type',$type)
            ->latest()
            ->first();
    }

    public function expireCode(VerificationCode $verificationCode): bool
    {
        return $verificationCode->update([
            'expired_at' => Carbon::now()
        ]);
    }

    /*public function isValidCode(string $code, string $to, string $via,string $type): bool
    {
        $verificationCode = $this->model->where('to',$to)
            ->where('via', $via)
            ->where('type', $type)
            ->latest()
            ->first();

        return !empty($verificationCode) &&
            $this->decryptCode($verificationCode->code) === $code &&
            Carbon::now()->lessThan($verificationCode->expired_at);
    }*/

    public function generateCode(): int
    {
        return random_int(100000,999999);
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
