<?php

namespace App\Enums\Company\LoyaltyProgramm;

enum DiscountTypeEnum: int
{
    case PERCENTAGE = 0;
    case FIXED_AMOUNT = 1;

    public function label(): string
    {
        return match ($this) {
            self::PERCENTAGE => 'Percentage',
            self::FIXED_AMOUNT => 'Fixed Amount',
        };
    }
}
