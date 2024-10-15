<?php

namespace App\Enums\Company;

use App\Enums\EnumMethods;

enum CardTypeEnum: int
{
    use EnumMethods;

    case UZCARD = 1;
    case HUMO = 2;

    public static function getCardType($cardNumber): CardTypeEnum
    {
        if (in_array(substr($cardNumber, 0, 4), CardTypeEnum::binHumo())) {
            return self::HUMO;
        } else {
            return self::UZCARD;
        }

    }

    public static function binUzkard(): array
    {
        return ['8600', '6262', '5440', '5614', '4357'];
    }

    public static function binHumo(): array
    {
        return ['9860', '4073', '5555'];
    }
}
