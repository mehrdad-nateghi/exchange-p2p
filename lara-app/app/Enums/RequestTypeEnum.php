<?php

namespace App\Enums;

use App\Traits\Global\EnumTrait;

enum RequestTypeEnum: int{
    use EnumTrait;

    case BUY = 1;
    case SELL = 2;

    public function getKeyLowercase(): string
    {
        return strtolower($this->name);
    }

    /*public static function fromName(string $name): ?self
    {
        return match (strtoupper($name)) {
            'BUY' => self::BUY,
            'SELL' => self::SELL,
            default => null,
        };
    }*/
}
