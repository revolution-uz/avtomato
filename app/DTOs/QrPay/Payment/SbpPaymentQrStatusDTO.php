<?php

namespace App\DTOs\QrPay\Payment;

use App\DTOs\QrPay\Base\BaseQrPayDTO;

/**
 * Class SbpPaymentQrStatusDTO
 *
 * @property bool|null $locked
 * @property string|null $pay_status
 * @property string|null $pay_trx_id
 */
class SbpPaymentQrStatusDTO extends BaseQrPayDTO
{
    public function __construct(
        public ?bool $locked,
        public ?string $pay_status,
        public ?string $pay_trx_id = null
    ) {}
}
