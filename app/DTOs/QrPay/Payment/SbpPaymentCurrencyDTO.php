<?php

namespace App\DTOs\QrPay\Payment;

use App\DTOs\QrPay\Base\BaseQrPayDTO;

/**
 * Class SbpPaymentCurrencyDTO
 *
 * @property float $rate
 */
class SbpPaymentCurrencyDTO extends BaseQrPayDTO
{
    public function __construct(
        public float $rate
    ) {}
}
