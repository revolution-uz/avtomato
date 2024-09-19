<?php

namespace App\Helpers;

class CardHelper
{
    public static function getNameByCardNumber(?string $cardNumber): string
    {
        return self::isUzcard($cardNumber) ? 'UZCARD' : 'HUMO';
    }

    public static function isUzcard($cardNumber): bool
    {
        if (is_null($cardNumber)) {
            return false;
        }
        if (str_starts_with($cardNumber, '9860')) {
            return false;
        }
        if (! is_numeric($cardNumber)) {
            return true;
        }

        return str_starts_with($cardNumber, '8600')
            or str_starts_with($cardNumber, '5614')
            or str_starts_with($cardNumber, '6262');
    }
}
