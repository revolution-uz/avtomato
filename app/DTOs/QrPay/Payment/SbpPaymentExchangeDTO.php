<?php

namespace App\DTOs\QrPay\Payment;

use App\DTOs\QrPay\Base\BaseQrPayDTO;

/**
 * Class SbpPaymentExchangeDTO
 *
 * @property float $rate
 */
class SbpPaymentExchangeDTO extends BaseQrPayDTO
{
    public function __construct(
        public float $rate
    ) {}
}
