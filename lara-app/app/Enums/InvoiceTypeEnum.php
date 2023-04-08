<?php

namespace App\Enums;

enum InvoiceTypeEnum: int{
    case RialPending = 0;
    case TargetPending = 1;
    case SystemPending = 2;
}
