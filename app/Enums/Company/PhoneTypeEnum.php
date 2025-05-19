<?php

namespace App\Enums\Company;

use App\Enums\EnumMethods;

enum PhoneTypeEnum: int
{
    use EnumMethods;

    // customer
    case PRIVATE = 1;
    case WORK = 2;
    case RELATIVE = 3;

    public static function customerValues(): array
    {
        return array_column(self::customerCases(), 'value');
    }

    public static function customerCases(): array
    {
        return [self::WORK, self::PRIVATE, self::RELATIVE];
    }
}
