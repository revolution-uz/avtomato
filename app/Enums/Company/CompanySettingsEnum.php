<?php

namespace App\Enums\Company;

use App\Enums\Main\SettingsTypeEnum;

enum CompanySettingsEnum: string
{
    case CARD_AFTER_DUE_DAYS = 'card_after_due_days';
    case PINFL_AFTER_DUE_DAYS = 'pinfl_after_due_days';
    case GUARANTOR_AFTER_DUE_DAYS = 'guarantor_after_due_days';
    case GUARANTOR_PINFL_AFTER_DUE_DAYS = 'guarantor_pinfl_after_due_days';

    public static function getType(self $type): SettingsTypeEnum
    {
        return match ($type) {
            self::CARD_AFTER_DUE_DAYS, self::PINFL_AFTER_DUE_DAYS, self::GUARANTOR_AFTER_DUE_DAYS, self::GUARANTOR_PINFL_AFTER_DUE_DAYS => SettingsTypeEnum::INT,
        };
    }

    public static function getDefaultValue(self $type): string|int
    {
        return match ($type) {
            self::CARD_AFTER_DUE_DAYS => 1,
            self::PINFL_AFTER_DUE_DAYS => 5,
            self::GUARANTOR_AFTER_DUE_DAYS => 10,
            self::GUARANTOR_PINFL_AFTER_DUE_DAYS => 15,
        };
    }
}
