<?php

namespace App\Enums;

enum VerificationCodeTypeEnum: int{
    case VERIFICATION_EMAIL = 1;
    case VERIFICATION_MOBILE = 2;
    //case SET_PASSWORD = 2;
    //case RESET_PASSWORD = 3;
}
