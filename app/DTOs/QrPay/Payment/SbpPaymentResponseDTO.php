<?php

namespace App\DTOs\QrPay\Payment;

use App\DTOs\QrPay\Base\BaseQrPayDTO;

/**
 * Class SbpPaymentResponseDTO
 *
 * @property SbpPaymentCreditDTO $credit
 * @property SbpPaymentDebitDTO $debit
 * @property SbpPaymentSenderDTO $sender
 * @property SbpPaymentReceiverDTO $receiver
 * @property SbpPaymentCurrencyDTO $currency
 */
class SbpPaymentResponseDTO extends BaseQrPayDTO
{
    public function __construct(
        public ?SbpPaymentCreditDTO $credit,
        public ?SbpPaymentDebitDTO $debit,
        public ?SbpPaymentSenderDTO $sender,
        public ?SbpPaymentReceiverDTO $receiver,
        public ?SbpPaymentCurrencyDTO $currency,
    ) {}
}
