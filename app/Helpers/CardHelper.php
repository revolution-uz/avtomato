<?php

namespace App\Helpers;

use App\Enums\Company\CardTypeEnum;

class CardHelper
{
    public static function getNameByCardNumber(?string $cardNumber): string
    {
        return self::isUzcard($cardNumber) ? CardTypeEnum::UZCARD->name : CardTypeEnum::HUMO->name;
    }

    public static function isUzcard($cardNumber): bool
    {
        if (is_null($cardNumber)) {
            return false;
        }
        if (in_array(substr($cardNumber, 0, 4), CardTypeEnum::binHumo())) {
            return false;
        }
        if (! is_numeric($cardNumber)) {
            return true;
        }

        return in_array(substr($cardNumber, 0, 4), CardTypeEnum::binUzkard());
    }
}
