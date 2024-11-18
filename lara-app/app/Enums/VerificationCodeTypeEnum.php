<?php

namespace App\Enums;

enum VerificationCodeTypeEnum: int{
    case SET_PASSWORD = 1;
    case RESET_PASSWORD = 2;
}
