<?php

namespace App\Traits\Global;

use ReflectionClass;

trait EnumTrait
{
    public static function names(): array
    {
        return array_column(self::cases(), 'name');
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function key(): string
    {
        return strtolower($this->name);
    }

    public static function keys(): array
    {
        return array_map(fn($case) => $case->key(), self::cases());
    }

    public static function array(): array
    {
        return array_combine(self::values(), self::names());
    }

    public static function randomValue(): mixed
    {
        $enum = self::values();
        return $enum[array_rand($enum)];
    }

    public static function randomName(): string
    {
        $enum = self::names();
        return $enum[array_rand($enum)];
    }

    public static function random(): self
    {
        $enums = self::cases();
        return $enums[array_rand($enums)];
    }

    public static function fromName(string $name): ?self
    {
        foreach (self::cases() as $case) {
            if (strcasecmp($case->name, $name) === 0) {
                return $case;
            }
        }
        return null;
    }

    public static function fromValue(mixed $value): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->value === $value) {
                return $case;
            }
        }
        return null;
    }

    public static function tryFromName(string $name): ?self
    {
        return self::fromName($name);
    }

    public static function tryFromValue(mixed $value): ?self
    {
        return self::fromValue($value);
    }

    public static function hasValue(mixed $value): bool
    {
        return in_array($value, self::values(), true);
    }

    public static function hasName(string $name): bool
    {
        return in_array(strtoupper($name), array_map('strtoupper', self::names()), true);
    }

    public static function description(): string
    {
        $reflect = new ReflectionClass(static::class);
        return $reflect->getDocComment() ?: '';
    }

    public static function options(): array
    {
        return array_map(function ($case) {
            return [
                'value' => $case->value,
                'name' => $case->name,
                'label' => $case->label(),
            ];
        }, self::cases());
    }

    public function label(): string
    {
        if (method_exists($this, 'customLabel')) {
            return $this->customLabel();
        }
        return ucwords(strtolower(str_replace('_', ' ', $this->name)));
    }

    public static function validate(mixed $value): bool
    {
        return self::hasValue($value);
    }

    public static function toSelectArray(): array
    {
        return array_combine(
            array_map(fn($case) => $case->value, self::cases()),
            array_map(fn($case) => $case->label(), self::cases())
        );
    }

    public static function fromKey(string $key): ?self
    {
        return self::fromName($key);
    }
}
