<?php

namespace App\Enums\Company;

use App\Enums\EnumMethods;

enum AllowedEnum: string
{
    use EnumMethods;

    case PHONE = '998904333699';
    case OTP_CODE = '111111';
}
