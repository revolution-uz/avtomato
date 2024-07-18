<?php

namespace App\Enums\Company;

use App\Enums\EnumMethods;

enum InstallmentGraphStatusEnum: int
{
    use EnumMethods;

    case NOT_STARTED = 0; // means not paid and it's early to charge
    case PAID = 1; // means paid (closed)
    case DELAY = 2; // means overdue or not paid fully
}
