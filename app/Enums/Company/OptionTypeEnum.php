<?php

namespace App\Enums\Company;

use App\Enums\EnumMethods;

enum OptionTypeEnum: string
{
    use EnumMethods;

    case MULTIPLE_CHOICE = 'multiple_choice';
    case RANGED_VALUE = 'ranged_value';
}
