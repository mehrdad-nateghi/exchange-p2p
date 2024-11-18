<?php

namespace App\Enums;

use App\Traits\Global\EnumTrait;

enum RequestStatusEnum: int{

    use EnumTrait;
    case PENDING = 1;
    case PROCESSING = 2;
    case TRADING = 3;
    case COMPLETED = 4;
    case CANCELED = 5;
}
