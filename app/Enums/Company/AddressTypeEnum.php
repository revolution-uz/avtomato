<?php

namespace App\Enums\Company;

use App\Enums\EnumMethods;

enum AddressTypeEnum: int
{
    use EnumMethods;
    case BIRTH_PLACE = 0;
    case LIVING_PLACE = 1;
    case TEMPORARY_PLACE = 2;
}
