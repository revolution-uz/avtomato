<?php

namespace App\DTOs\QrPay\Payment;

use App\DTOs\QrPay\Base\BaseQrPayDTO;

/**
 * Class SbpPaymentSenderDTO
 *
 * @property string|null $number
 * @property string|null $expire
 */
class SbpPaymentSenderDTO extends BaseQrPayDTO
{
    public function __construct(
        public ?string $number,
        public ?string $expire,
    ) {}
}
