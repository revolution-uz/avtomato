<?php

namespace App\DTOs\QrPay\Payment;

use App\DTOs\QrPay\Base\BaseQrPayDTO;

/**
 * Class SbpCreatePaymentRequestDTO
 *
 * @property string $ext_id
 */
class SbpCreatePaymentRequestDTO extends BaseQrPayDTO
{
    public function __construct(
        public string $ext_id,
        public int $amount,
        public string $purpose_code,
        public int $terminal_id,
        public string $redirect_url,

    ) {}
}
