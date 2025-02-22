<?php

namespace App\DTOs\QrPay\Payment;

use App\DTOs\QrPay\Base\BaseQrPayDTO;

/**
 * Class SbpCreateTransferRequestDTO
 *
 * @property string $ext_id
 * @property int $amount
 * @property int $currency
 * @property int $account
 */
class SbpCreateTransferRequestDTO extends BaseQrPayDTO
{
    public function __construct(
        public string $ext_id,
        public int $amount,
        public int $currency,
        public string $account,
    ) {}
}
