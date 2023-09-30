<?php

namespace App\Enums\Company;

use App\Enums\EnumMethods;

enum PhoneTypeEnum: int
{
    use EnumMethods;

    case PRIVATE = 1;
    case WORK = 2;
    case RELATIVE = 3;
}
