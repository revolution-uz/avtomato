<?php

namespace App\DTOs\QrPay\Payment;

enum UniredPaymentCreditState: string
{
    case CHEQUE_CREATED = '-2';
    case CHEQUE_CREATED_2 = '-1';
    case SUCCESS = '0';
    case CANCELLED = '-21';
    case UNKNOWN_ERROR = '-99';

    public static function getStatusText(string|self|null $state): string
    {
        $value = is_string($state) ? self::tryFrom($state) : $state;

        return match ($value) {
            self::CHEQUE_CREATED, self::CHEQUE_CREATED_2 => 'Чек создан ☑️',
            self::SUCCESS => 'Чек успешно оплачен ✅',
            self::CANCELLED => 'Платеж отменен ❌',
            self::UNKNOWN_ERROR => 'Неизвестная ошибка ⛔️',
            default => 'Unired запрос не выполнен ⛔️',
        };
    }
}
