<?php

namespace App\Enums\Company;

use App\Enums\EnumMethods;

enum CustomerTypeEnum: int
{
    use EnumMethods;

    case INSTALLMENT = 1;
    case ECOMMERCE = 2;
    case INSTALLMENT_AND_ECOMMERCE = 3;
}
