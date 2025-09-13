<?php

namespace App\Data;


use App\Enums\VerificationCodeTypeEnum;
use App\Enums\VerificationCodeViaEnum;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;

class VerificationCodeData extends Data
{
    public ?string $code;
    public string $to;
    public \App\Enums\VerificationCodeViaEnum $via;
    public VerificationCodeTypeEnum $type;

    public static function rules(ValidationContext $context): array
    {
        $via = (int) $context->payload['via'] ?? null;

        return [
            'code' => ['required','string'],
            'to' => [
                'required',
                Rule::when($via === VerificationCodeViaEnum::EMAIL->value, fn () => 'email:filter')
            ],
            'via' => ['required', [Rule::In(VerificationCodeViaEnum::EMAIL->value)]],
            'type' => ['required', [Rule::In(VerificationCodeTypeEnum::SET_PASSWORD->value)]],
        ];
    }
}