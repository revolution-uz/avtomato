<?php

namespace App\Enums\Company;

use App\Enums\EnumMethods;

enum OrderStatusEnum: string
{
    use EnumMethods;

    case CREATED = 'Created';
    case CANCELLED = 'Cancelled';
    case FINISHED = 'Finished';
}
