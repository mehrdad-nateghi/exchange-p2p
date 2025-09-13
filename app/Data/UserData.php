<?php

namespace App\Data;


use App\Enums\VerificationCodeTypeEnum;
use App\Enums\VerificationCodeViaEnum;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Support\Validation\ValidationContext;

class UserData extends Data
{
    //public ?string $id;
    public ?string $uuid;
    public ?string $first_name;
    public ?string $last_name;
    public ?string $email;
    //public ?string $status;
    public ?\DateTime $created_at;
    public ?\DateTime $updated_at;

    public static function rules(ValidationContext $context): array
    {
        //
    }
}