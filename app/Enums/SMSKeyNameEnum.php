<?php

namespace App\Enums;

use App\Traits\Global\EnumTrait;

enum SMSKeyNameEnum: string{
    use EnumTrait;

    case SEND_VERIFICATION_CODE = 'send_verification_code';
}
