<?php

namespace App\Services\API\V1;


use App\Data\API\V1\VerificationCodeData;
use App\Models\VerificationCode;
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
}
