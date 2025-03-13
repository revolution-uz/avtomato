<?php

namespace App\Enums\Company\LoyaltyProgramm;

enum OrderPaymentStatusEnum: int
{
    case PENDING = 0;
    case COMPLETED = 1;
    case FAILED = 2;
    case REFUNDED = 3;

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::COMPLETED => 'Completed',
            self::FAILED => 'Failed',
            self::REFUNDED => 'Refunded',
        };
    }
}
