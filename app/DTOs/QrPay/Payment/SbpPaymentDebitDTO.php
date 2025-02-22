<?php

namespace App\DTOs\QrPay\Payment;

use App\DTOs\QrPay\Base\BaseQrPayDTO;

/**
 * Class SbpPaymentQrDTO
 *
 * @property int|null $operation_id
 * @property string $order_id
 * @property string $state
 * @property string $description
 * @property int $amount
 * @property int $currency
 * @property string $url
 * @property int|null $qrc_id
 */
class SbpPaymentDebitDTO extends BaseQrPayDTO
{
    public function __construct(
        public ?int $operation_id,
        public ?string $order_id,
        public ?string $state,
        public ?string $description,
        public ?int $amount,
        public ?int $currency,
        public ?string $url,
        public ?int $qrc_id,
    ) {}

    public function isSuccess(): bool
    {
        return $this->state == PaygineDebitState::SUCCESS->value;
    }
}
