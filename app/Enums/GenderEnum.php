<?php

namespace App\Enums;

enum GenderEnum: int
{
    use EnumMethods;

    case MALE = 0;
    case FEMALE = 1;

    public static function convertFromOilaKredit(int $oilaKreditGender): GenderEnum
    {
        return $oilaKreditGender === 1 ? self::MALE : self::FEMALE;
    }
}
