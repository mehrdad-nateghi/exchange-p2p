<?php

namespace App\Enums\API\V1;

enum VerificationCodeViaEnum: int{
    case EMAIL = 1;
    case MOBILE = 2;
}
