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
    // installment
    case INSTALLMENT_AUTO_CHARGE = 4;
    case INSTALLMENT_NOTIFICATION = 5;
    case INSTALLMENT_PAYMENT = 6;

    public static function customerCases(): array
    {
        return [self::WORK, self::PRIVATE, self::RELATIVE];
    }

    public static function customerValues(): array
    {
        return array_column(self::customerCases(), 'value');
    }

    public static function installmentCases(): array
    {
        return [self::INSTALLMENT_AUTO_CHARGE, self::INSTALLMENT_NOTIFICATION, self::INSTALLMENT_PAYMENT];
    }

    public static function installmentValues(): array
    {
        return array_column(self::installmentCases(), 'value');
    }
}
