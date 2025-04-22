<?php

namespace App\Enums\Company\Scoring;

enum CardScoringTypeCodeEnum: int
{
    case PURCHASE = 205; // Покупка/Оплата
    case TRANSFER = 206; // Перевод на карту (P2P)
    case CASH_WITHDRAWAL = 207; // Снятие наличных
    case ATM_REPLENISHMENT = 208; // Пополнение счёта (наличные в банкомате)
    case REFUND = 225; // Возврат (отмена) покупки

    public static function getDescription(int $value): string
    {
        return match ($value) {
            self::PURCHASE->value => 'Purchase Payment (P2P Send)',
            self::TRANSFER->value => 'Transfer crediting (P2P crediting)',
            self::CASH_WITHDRAWAL->value => 'Cash withdrawal',
            self::ATM_REPLENISHMENT->value => 'Account replenishment via cash in ATM',
            self::REFUND->value => 'Refund (cancellation) of purchase',
            default => 'Unknown type',
        };
    }
}
