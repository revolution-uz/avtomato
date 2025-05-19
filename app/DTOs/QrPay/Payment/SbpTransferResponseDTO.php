<?php

namespace App\DTOs\QrPay\Payment;

use App\DTOs\QrPay\Base\BaseQrPayDTO;

/**
 * Class SbpTransferResponseDTO
 *
 * @property array $credit
 * @property array $debit
 * @property array $sender
 * @property array $receiver
 * @property SbpPaymentCurrencyDTO $currency
 */
class SbpTransferResponseDTO extends BaseQrPayDTO
{
    public function __construct(
        public ?array $credit,
        public ?array $debit,
        public ?array $sender,
        public ?array $receiver,
        public ?SbpPaymentCurrencyDTO $currency,
    ) {}
}
