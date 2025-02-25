<?php

namespace App\DTOs\QrPay\Payment;

use App\DTOs\QrPay\Base\BaseQrPayDTO;
use App\Helpers\Utilities;

/**
 * Class SbpPaymentCreditDTO
 *
 * @property string $ext_id
 * @property int $state
 * @property float $amount
 * @property string $currency
 */
class SbpPaymentCreditDTO extends BaseQrPayDTO
{
    public function __construct(
        public string $ext_id,
        public ?int $state,
        public float $amount,
        public string $currency,
    ) {}

    public function getCompanyId(): ?string
    {
        $arr = explode(Utilities::TENANT_SEPARATOR, $this->ext_id);

        return $arr[0] ?? null;
    }

    public function isSuccess(): bool
    {
        return $this->state == UniredPaymentCreditState::SUCCESS->value;
    }

    public function isFailed(): bool
    {
        return $this->state == UniredPaymentCreditState::UNKNOWN_ERROR->value;
    }

    public function isCancelled(): bool
    {
        return $this->state == UniredPaymentCreditState::CANCELLED->value;
    }
}
