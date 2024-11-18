<?php

namespace App\Enums;

use App\Traits\Global\EnumTrait;

enum InvoiceStatusEnum: int{

    use EnumTrait;

    case PENDING = 1;
    case PAID = 2;
    case FAILED = 3;
    case CANCELED = 4;

}
