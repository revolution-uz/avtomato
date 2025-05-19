<?php

namespace App\Enums\Company;

use App\Enums\EnumMethods;

enum SmsTemplateStatusEnum: int
{
    use EnumMethods;

    case DRAFT = 0;
    case ACTIVE = 1;
    case INACTIVE = 2;
}
