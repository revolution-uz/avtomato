<?php

namespace App\Enums\Company;

use App\Enums\EnumMethods;

enum TransactionAttemptTypeEnum: int
{
    use EnumMethods;

    case ATMOS_PAY_WITH_CARD = 0;
    case ATMOS_PAY_WITH_PINFL = 1;
}
