<?php

namespace App\Enums\Company;

use App\Enums\EnumMethods;

enum PaymentStatusEnum: int
{
    use EnumMethods;

    case CANCELED = 0;
    case WAITING_FOR_CONFIRMATION = 1;
    case SUCCESS = 2;
}
