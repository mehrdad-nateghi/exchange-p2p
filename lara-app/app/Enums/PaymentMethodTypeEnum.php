<?php

namespace App\Enums;

enum PaymentMethodTypeEnum: int{
    case RIAL_BANK = 1;
    case FOREIGN_BANK = 2;
    case PAYPAL = 3;
}
