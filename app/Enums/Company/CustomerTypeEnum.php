<?php

namespace App\Enums\Company;

use App\Enums\EnumMethods;

enum CustomerTypeEnum: int
{
    use EnumMethods;

    case INSTALLMENT = 1;
    case MARKET = 2;
    case INSTALLMENT_AND_MARKET = 3;
}
