<?php

namespace App\DTOs\QrPay\Payment;

enum PaygineDebitState: string
{
    case FORM_CREATED = 'REGISTERED';
    case SUCCESS = 'ACWP';
    case CANCELLED = 'REJECTED';

    public static function getStatusText(string|self|null $state): string
    {
        $enum = is_string($state) ? self::tryFrom($state) : $state;

        return match ($enum) {
            self::FORM_CREATED => 'Чек создан ☑️',
            self::SUCCESS => 'Успешно ✅',
            self::CANCELLED => 'Отменен ❌',
            default => 'Unired запрос не выполнен ⛔️',
        };
    }
}
