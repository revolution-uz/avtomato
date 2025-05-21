<?php

namespace App\Enums\Company\LoyaltyProgram;

use App\Enums\EnumMethods;

enum CanLoginTypeEnum: int
{
    use EnumMethods;

    case CAN_LOGIN = 0;
    case LAST_CHANCE = 1;
    case CANNOT_LOGIN = 2;

    public function isValid(): bool
    {
        return $this !== self::CANNOT_LOGIN;
    }
}
