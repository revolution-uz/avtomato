<?php

namespace App\Enums\External;

use App\Enums\EnumMethods;

enum IcanTransactionStatusEnum: string
{
    use EnumMethods;

    case SUCCESS = 'completed';
    case CANCELED = 'canceled';
}
