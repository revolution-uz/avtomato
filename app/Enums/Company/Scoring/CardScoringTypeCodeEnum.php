<?php

namespace App\Enums\Company\Scoring;

enum CardScoringTypeCodeEnum: int
{
    // Humo
    case PURCHASE = 205; // Покупка/Оплата
    case TRANSFER = 206; // Перевод на карту (P2P)
    case CASH_WITHDRAWAL = 207; // Снятие наличных
    case ATM_REPLENISHMENT = 208; // Пополнение счёта (наличные в банкомате)
    case REFUND = 225; // Возврат (отмена) покупки

    // Uzcard
    case GOODS_SERVICES_PAYMENT_NON_P2P = 700; // Goods/services payment (non-P2P)
    case TEZ_QR_PAYMENT = 775; // "Tez QR" payment
    case P2P_PAYMENT_PROCESSING = 785; // P2P payment processing
    case ELECTRONIC_PAYMENTS_ALL = 508; // Electronic payments (all)
    case E_POS_VIRTUAL_TERMINAL = 620; // E-POS virtual terminal
    case E_POS_OTHERS = 680; // E-POS (others)
    case TAX_PAYMENTS = 626; // Tax payments
    case CHARITY_DONATIONS = 653; // Charity donations
    case STATE_DUTIES = 654; // State duties
    case GOV_SERVICES_PAYMENTS = 655; // Gov services payments
    case MANDATORY_PENALTIES = 656; // Mandatory penalties
    case E_AUCTION_PAYMENTS = 657; // E-auction payments
    case TRANSPORT_TICKET_PAYMENTS = 658; // Transport ticket payments
    case UTILITY_PAYMENTS = 659; // Utility payments

    public static function getDescription(int $value): string
    {
        return match ($value) {
            self::PURCHASE->value => 'Purchase Payment (P2P Send)',
            self::TRANSFER->value => 'Transfer crediting (P2P crediting)',
            self::CASH_WITHDRAWAL->value => 'Cash withdrawal',
            self::ATM_REPLENISHMENT->value => 'Account replenishment via cash in ATM',
            self::REFUND->value => 'Refund (cancellation) of purchase',
            self::GOODS_SERVICES_PAYMENT_NON_P2P->value => 'Goods/services payment (non-P2P)',
            self::TEZ_QR_PAYMENT->value => '"Tez QR" payment',
            self::P2P_PAYMENT_PROCESSING->value => 'P2P payment processing',
            self::ELECTRONIC_PAYMENTS_ALL->value => 'Electronic payments (all)',
            self::E_POS_VIRTUAL_TERMINAL->value => 'E-POS virtual terminal',
            self::E_POS_OTHERS->value => 'E-POS (others)',
            self::TAX_PAYMENTS->value => 'Tax payments',
            self::CHARITY_DONATIONS->value => 'Charity donations',
            self::STATE_DUTIES->value => 'State duties',
            self::GOV_SERVICES_PAYMENTS->value => 'Gov services payments',
            self::MANDATORY_PENALTIES->value => 'Mandatory penalties',
            self::E_AUCTION_PAYMENTS->value => 'E-auction payments',
            self::TRANSPORT_TICKET_PAYMENTS->value => 'Transport ticket payments',
            self::UTILITY_PAYMENTS->value => 'Utility payments',
            default => 'Unknown type',
        };
    }
}
