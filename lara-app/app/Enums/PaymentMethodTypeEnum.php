<?php

namespace App\Enums;

use App\Traits\Global\EnumTrait;

enum PaymentMethodTypeEnum: int{
    use EnumTrait;

    case RIAL_BANK = 1;
    case FOREIGN_BANK = 2;
    case PAYPAL = 3;

    public function getKeyLowercase(): string
    {
        return strtolower($this->name);
    }
}
