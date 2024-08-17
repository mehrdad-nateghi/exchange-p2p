<?php

namespace App\Enums;

use App\Traits\Global\EnumTrait;

enum InvoiceTypeEnum: int{

    use EnumTrait;

    case STEP_ONE_PAY_TOMAN_TO_SYSTEM = 1;
}
