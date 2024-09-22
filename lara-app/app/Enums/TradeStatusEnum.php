<?php

namespace App\Enums;

use App\Traits\Global\EnumTrait;

enum TradeStatusEnum: int{

    use EnumTrait;

    case PROCESSING = 1;
    case COMPLETED = 2;
    case CANCELED = 3;
    case FAILED = 4;
}
