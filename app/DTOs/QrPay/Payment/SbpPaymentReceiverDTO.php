<?php

namespace App\DTOs\QrPay\Payment;

use App\DTOs\QrPay\Base\BaseQrPayDTO;

/**
 * Class SbpPaymentReceiverDTO
 *
 * @property string|null $owner
 * @property string|null $number
 */
class SbpPaymentReceiverDTO extends BaseQrPayDTO
{
    public function __construct(
        public ?string $owner,
        public ?string $expire,
    ) {}
}
