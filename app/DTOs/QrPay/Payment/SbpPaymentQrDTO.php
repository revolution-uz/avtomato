<?php

namespace App\DTOs\QrPay\Payment;

use App\DTOs\QrPay\Base\BaseQrPayDTO;

/**
 * Class SbpPaymentQrDTO
 *
 * @property float $amount
 * @property int $currency
 * @property string $qrc_id
 * @property int $local_qrc_id
 * @property string $url
 * @property SbpPaymentQrStatusDTO $status
 */
class SbpPaymentQrDTO extends BaseQrPayDTO
{
    public function __construct(
        public ?float $amount,
        public ?int $currency,
        public ?string $qrc_id,
        public ?int $local_qrc_id,
        public ?string $url,
        public ?SbpPaymentQrStatusDTO $status,
        public ?SbpPaymentQrStatusDTO $refund_status
    ) {}
}
