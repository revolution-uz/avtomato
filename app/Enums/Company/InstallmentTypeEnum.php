<?php

namespace App\Enums\Company;

use App\Enums\EnumMethods;

enum InstallmentTypeEnum: int
{
    use EnumMethods;

    case WITHOUT_CARD = 0;
    case WITH_CARD = 1;
    case WITH_PINFL = 2;
    case WITH_BOTH = 3;

    public static function options(): array
    {
        return [
            self::WITHOUT_CARD->value => 'Without card',
            self::WITH_CARD->value => 'With card',
            self::WITH_PINFL->value => 'With pinfl',
            self::WITH_BOTH->value => 'With both',
        ];
    }
}
