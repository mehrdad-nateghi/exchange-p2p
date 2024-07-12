<?php

namespace App\Enums;

enum RequestStatusEnum: int{
    case PENDING = 1;
    case PROCESSING = 2;
    case TRADING = 3;
    case CANCELED = 4;

    public function getKeyLowercase(): string
    {
        return strtolower($this->name);
    }

    public static function fromName(string $name): ?self
    {
        return match (strtoupper($name)) {
            'PENDING' => self::PENDING,
            'PROCESSING' => self::PROCESSING,
            'TRADING' => self::TRADING,
            'CANCELED' => self::CANCELED,
            default => null,
        };
    }
}
