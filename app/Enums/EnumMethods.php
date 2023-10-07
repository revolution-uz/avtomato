<?php

namespace App\Enums;

trait EnumMethods
{
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function randomValue(): string
    {
        $values = self::values();

        return $values[array_rand($values)];
    }
}
