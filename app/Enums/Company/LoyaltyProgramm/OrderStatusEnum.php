<?php

namespace App\Enums\Company\LoyaltyProgramm;

enum OrderStatusEnum: int
{
    case PENDING = 0;
    case COMPLETED = 1;
    case CANCELED = 2;
    case REFUNDED = 3;

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::COMPLETED => 'Completed',
            self::CANCELED => 'Canceled',
            self::REFUNDED => 'Refunded',
        };
    }
}
