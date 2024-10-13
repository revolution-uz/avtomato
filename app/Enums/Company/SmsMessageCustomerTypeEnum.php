<?php

namespace App\Enums\Company;

use App\Enums\EnumMethods;

enum SmsMessageCustomerTypeEnum: int
{
    use EnumMethods;

    case OTHERS = 0;
    case CUSTOMER = 1;
    case GUARANTOR = 2;
}
