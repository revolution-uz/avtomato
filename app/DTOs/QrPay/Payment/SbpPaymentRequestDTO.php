<?php

namespace App\DTOs\QrPay\Payment;

use App\DTOs\QrPay\Base\BaseQrPayDTO;

/**
 * Class SbpCancelPaymentRequestDTO
 *
 * @property string $ext_id
 */
class SbpPaymentRequestDTO extends BaseQrPayDTO
{
    public function __construct(
        public string $ext_id,
    ) {}
}
