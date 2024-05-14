<?php

namespace App\Enums\Company;

use App\Enums\EnumMethods;

enum AddressTypeEnum: int
{
    use EnumMethods;

    case BIRTH_PLACE = 0;
    case LIVING_PLACE = 1;
    case TEMPORARY_PLACE = 2;
    case WORK_PLACE = 3;

    public static function getName(int $type): string
    {
        return match ($type) {
            self::BIRTH_PLACE->value => self::BIRTH_PLACE->name,
            self::LIVING_PLACE->value => self::LIVING_PLACE->name,
            self::TEMPORARY_PLACE->value => self::TEMPORARY_PLACE->name,
            self::WORK_PLACE->value => self::WORK_PLACE->name,
            default => 'Not found'
        };
    }
}
