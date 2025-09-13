<?php

namespace App\Enums;

enum VerificationCodeViaEnum: int{
    case EMAIL = 1;
    case MOBILE = 2;
}
