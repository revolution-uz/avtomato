<?php

namespace App\DTOs\QrPay\Payment;

use App\DTOs\QrPay\Base\BaseQrPayDTO;

/**
 * Class SbpCancelPaymentRequestDTO
 *
 * @property string $ext_id
 * @property string $transaction_id
 */
class SbpCancelPaymentRequestDTOBase extends BaseQrPayDTO
{
    public function __construct(
        public string $ext_id,
        public string $transaction_id,
    ) {}
}
