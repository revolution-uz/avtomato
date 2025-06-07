<?php

namespace App\Enums\Company;

use App\Enums\EnumMethods;

enum CardTypeEnum: int
{
    use EnumMethods;

    case UZCARD = 1;
    case HUMO = 2;

    public static function getCardType($cardNumber): ?CardTypeEnum
    {
        $bin = substr($cardNumber, 0, 4);

        if (in_array($bin, CardTypeEnum::binHumo(), true)) {
            return self::HUMO;
        } elseif (in_array($bin, CardTypeEnum::binUzcard(), true)) {
            return self::UZCARD;
        }

        return null;
    }

    public static function binUzcard(): array
    {
        return ['8600', '6262', '5440', '5614', '4357'];
    }

    public static function binHumo(): array
    {
        return ['9860', '4073', '5555', '4008', '4027', '4062', '4067', '4097', '4198', '4916'];
    }

    public static function getAllCasesForDropdown(): array
    {
        return [
            self::UZCARD->value => 'Uzcard',
            self::HUMO->value => 'Humo',
        ];
    }
}
