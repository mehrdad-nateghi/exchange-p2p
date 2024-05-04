<?php

namespace App\Data;


use App\Enums\SendCodeTypeEnum;
use App\Enums\SendCodeViaEnum;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Data;

class VerificationCodeData extends Data
{
    public SendCodeViaEnum $via;
    public SendCodeTypeEnum $type;

    public static function rules(): array
    {
        return [
            'via' => ['required', [Rule::enum(SendCodeViaEnum::class)]],
            'type' => ['required', [Rule::enum(SendCodeTypeEnum::class)]],
        ];
    }
}