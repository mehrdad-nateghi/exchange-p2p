<?php

namespace App\Enums;

use App\Traits\Global\EnumTrait;

enum TransactionProviderEnum: int{

    use EnumTrait;

    case FINNOTECH = 1;
    case DIRECT_DEPOSIT = 2;
}
