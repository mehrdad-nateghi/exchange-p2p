<?php

namespace App\Enums\API\V1;

enum VerificationCodeTypeEnum: int{
    case SET_PASSWORD = 1;
    case RESET_PASSWORD = 2;
}
