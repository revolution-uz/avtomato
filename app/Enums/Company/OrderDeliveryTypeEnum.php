<?php

namespace App\Enums\Company;

use App\Enums\EnumMethods;

enum OrderDeliveryTypeEnum: int
{
    use EnumMethods;

    case DELIVERY_TO_ADDRESS = 0;
    case SELF_PICKUP = 1;

    public static function getName(int $type): string
    {
        return match ($type) {
            self::DELIVERY_TO_ADDRESS->value => self::DELIVERY_TO_ADDRESS->name,
            self::SELF_PICKUP->value => self::SELF_PICKUP->name,
            default => 'Unknown',
        };
    }
}
