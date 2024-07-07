<?php

namespace App\Enums;

enum RequestStatusEnum: int{
    case PROCESSING = 1;
    case TRADING = 2;
    case CANCELED = 3;

    public function getKeyLowercase(): string
    {
        return strtolower($this->name);
    }
}
