<?php

namespace App\DTOs\QrPay\Payment;

use App\DTOs\QrPay\Base\BaseQrPayDTO;
use Carbon\Carbon;

/**
 * Class SbpPaymentResponseDTO
 *
 * @property SbpPaymentChequeDTO $cheque
 * @property SbpPaymentExchangeDTO $exchange
 * @property SbpPaymentQrDTO $qr
 * @property SbpPaymentCallbackDTO $callback
 * @property Carbon $updated_at
 */
class SbpPaymentResponseDTO extends BaseQrPayDTO
{
    public function __construct(
        public ?SbpPaymentChequeDTO $cheque,
        public ?SbpPaymentExchangeDTO $exchange,
        public ?SbpPaymentQrDTO $qr,
        public ?SbpPaymentCallbackDTO $callback,
        public ?Carbon $updated_at,

    ) {}
}
