<?php

namespace App\Data\API\V1;


use App\Enums\API\V1\VerificationCodeTypeEnum;
use App\Enums\API\V1\VerificationCodeViaEnum;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Data;

class VerificationCodeData extends Data
{
    public string $to;
    public VerificationCodeViaEnum $via;
    public VerificationCodeTypeEnum $type;

    public static function rules(): array
    {
        return [
            'via' => ['required', [Rule::enum(VerificationCodeViaEnum::class)]],
            'type' => ['required', [Rule::enum(VerificationCodeTypeEnum::class)]],
        ];
    }
}