<?php

namespace App\Enums\Company;

use App\Enums\EnumMethods;

enum CustomerTypeEnum: int
{
    use EnumMethods;

    case INSTALLMENT = 1;
    case ECOMMERCE = 2;
    case INSTALLMENT_AND_ECOMMERCE = 3;

    public static function options(): array
    {
        return [
            self::INSTALLMENT->value => 'Installment',
            self::ECOMMERCE->value => 'Ecommerce',
            self::INSTALLMENT_AND_ECOMMERCE->value => 'Installment and ecommerce',
        ];
    }
}
