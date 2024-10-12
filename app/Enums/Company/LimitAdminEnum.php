<?php

namespace App\Enums\Company;

use App\Enums\EnumMethods;

enum LimitAdminEnum: int
{
    use EnumMethods;

    case MULTIBRANCH_1_ADMIN = 1;
    case MULTIBRANCH_2_ADMIN = 5;
    case MULTIBRANCH_3_ADMIN = 10;
    case MULTIBRANCH_4_ADMIN = 20;
}
