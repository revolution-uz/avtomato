<?php

namespace App\Enums\Company;

use App\Enums\EnumMethods;

enum CardStatusEnum: int
{
    use EnumMethods;
    case BLOCKED = -1;
    case INACTIVE = 0;
    case ACTIVE = 1;
}
