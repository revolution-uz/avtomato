<?php

namespace App\DTOs\QrPay\Payment;

use App\DTOs\QrPay\Base\BaseQrPayDTO;

/**
 * Class SbpPaymentCallbackDTO
 *
 * @property string|null $trx_id
 * @property string|null $trx_time
 * @property string|null $snd_pam
 * @property string|null $snd_phone_masked
 * @property string|null $add
 * @property string|null $at
 */
class SbpPaymentCallbackDTO extends BaseQrPayDTO
{
    public function __construct(
        public ?string $trx_id = null,
        public ?string $trx_time = null,
        public ?string $snd_pam = null,
        public ?string $snd_phone_masked = null,
        public ?string $add = null,
        public ?string $at = null
    ) {}
}
