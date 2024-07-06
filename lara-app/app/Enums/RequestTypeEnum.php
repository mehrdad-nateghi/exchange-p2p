<?php

namespace App\Enums;

enum RequestTypeEnum: int{
    case BUY = 1;
    case SELL = 2;

    public function getKeyLowercase(): string
    {
        return strtolower($this->name);
    }
}
