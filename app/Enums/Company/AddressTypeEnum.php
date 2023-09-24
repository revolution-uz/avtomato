<?php

namespace App\Enums\Company;

enum AddressTypeEnum: int
{
    case BIRTH_PLACE = 0;
    case LIVING_PLACE = 1;
    case TEMPORARY_PLACE = 2;
}
