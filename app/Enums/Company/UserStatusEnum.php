<?php

namespace App\Enums\Company;

use App\Enums\EnumMethods;

enum UserStatusEnum: int
{
    use EnumMethods;

    case ACTIVE = 1;
    case INACTIVE = 0;
}
