<?php

namespace App\Enums\Company;

use App\Enums\EnumMethods;

enum TransactionStatusEnum: int
{
    use EnumMethods;

    case SUCCESS = 1;
    case CANCELED = 0;
}
