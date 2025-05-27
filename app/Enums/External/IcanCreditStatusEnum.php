<?php

namespace App\Enums\External;

use App\Enums\EnumMethods;

enum IcanCreditStatusEnum: string
{
    use EnumMethods;

    case ACTIVE = 'active';
    case WAITING_FOR_CONFIRMATION = 'waiting_for_confirmation';
    case WAITING_FOR_INITIAL_PAYMENT = 'waiting_for_initial_payment';
    case COMPLETED = 'completed';
    case CANCELED = 'canceled';
}
