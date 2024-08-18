<?php

namespace App\Enums;

use App\Traits\Global\EnumTrait;

enum TransactionStatusEnum: int{

    use EnumTrait;

    case PENDING = 1;
    case COMPLETED = 2;
    case FAILED = 3;
}
