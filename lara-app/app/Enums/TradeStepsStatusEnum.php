<?php

namespace App\Enums;

use App\Traits\Global\EnumTrait;

enum TradeStepsStatusEnum: int{

    use EnumTrait;

    case TODO = 1;
    case DOING = 2;
    case DONE = 3;
}
