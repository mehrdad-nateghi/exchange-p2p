<?php

namespace App\Enums;

use App\Traits\Global\EnumTrait;

enum BidStatusEnum: int{

    use EnumTrait;

    case REGISTERED = 1;
    case ACCEPTED = 2;
    case REJECTED = 3;
}
