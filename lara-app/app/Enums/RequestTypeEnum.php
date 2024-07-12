<?php

namespace App\Enums;

enum RequestTypeEnum: int{
    case BUY = 1;
    case SELL = 2;

    public function getKeyLowercase(): string
    {
        return strtolower($this->name);
    }

    public static function fromName(string $name): ?self
    {
        return match (strtoupper($name)) {
            'BUY' => self::BUY,
            'SELL' => self::SELL,
            default => null,
        };
    }
}
