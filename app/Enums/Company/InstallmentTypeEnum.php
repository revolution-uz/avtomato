<?php

namespace App\Enums\Company;

use App\Enums\EnumMethods;

enum InstallmentTypeEnum: int
{
    use EnumMethods;

    case WITHOUT_CARD = 0;
    case WITH_CARD = 1;
    case WITH_PINFL = 2;
}
