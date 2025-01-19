<?php

namespace App\DTOs\QrPay\Payment;

use App\DTOs\QrPay\Base\BaseQrPayDTO;

/**
 * Class SbpPaymentDTO
 *
 * @property SbpPaymentResponseDTO $payment
 */
class SbpPaymentDTO extends BaseQrPayDTO
{
    public function __construct(
        public ?SbpPaymentResponseDTO $payment,
    ) {}
}
