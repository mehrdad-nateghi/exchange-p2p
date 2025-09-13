<?php

namespace App\Enums;

use App\Traits\Global\EnumTrait;

enum FinnotechTokenTypeEnum: int{
    use EnumTrait;

    case CLIENT_CREDENTIALS = 1;
    case AUTHORIZATION_CODE = 2;
}
