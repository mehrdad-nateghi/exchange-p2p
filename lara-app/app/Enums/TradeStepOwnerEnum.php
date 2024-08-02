<?php

namespace App\Enums;
use App\Traits\Global\EnumTrait;

enum TradeStepOwnerEnum: int
{
    use EnumTrait;

    case BUYER = 1;
    case SELLER = 2;
    case SYSTEM = 3;
}
