<?php

namespace App\Enums\Main;

enum SmsProviderEnum: int
{
    case SMS_FLY = 0;

    public static function getAllProviderItemsForDropdown(): array
    {
        return [
            self::SMS_FLY->value => self::SMS_FLY->name,
        ];
    }
}
