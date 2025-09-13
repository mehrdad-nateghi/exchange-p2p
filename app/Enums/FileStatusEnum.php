<?php

namespace App\Enums;

use App\Traits\Global\EnumTrait;

enum FileStatusEnum: int{

    use EnumTrait;

    case UPLOADED = 1;
    case ACCEPT_BY_BUYER = 2;
    case REJECT_BY_BUYER = 3;
    case ACCEPT_BY_ADMIN = 4;
}
