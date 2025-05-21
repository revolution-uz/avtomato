<?php

namespace App\Enums\Company\LoyaltyProgram;

use App\Enums\EnumMethods;

enum ShopStatusEnum: int
{
    use EnumMethods;

    case PASSIVE = -1;
    case WAITING = 0;
    case ACTIVE = 1;
}
