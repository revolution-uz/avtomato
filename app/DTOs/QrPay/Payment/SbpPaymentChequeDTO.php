<?php

namespace App\DTOs\QrPay\Payment;

use App\DTOs\QrPay\Base\BaseQrPayDTO;

/**
 * Class SbpPaymentChequeDTO
 *
 * @property string $partner
 * @property string $ext_id
 * @property float $amount
 * @property int $currency
 * @property string $purpose
 * @property string $redirect_url
 * @property string $merchant_id
 * @property string $created_at
 */
class SbpPaymentChequeDTO extends BaseQrPayDTO
{
    public function __construct(
        public string $partner,
        public string $ext_id,
        public float $amount,
        public int $currency,
        public string $purpose,
        public string $redirect_url,
        public string $merchant_id,
        public string $created_at
    ) {}
}
