<?php

namespace App\DTOs\QrPay\Payment;

use App\DTOs\QrPay\Base\BaseQrPayDTO;
use App\Helpers\Utilities;

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

    public function getCompanyId()
    {
        $arr = explode(Utilities::TENANT_SEPARATOR, $this->ext_id);

        return $arr[0] ?? null;
    }

    public function getTerminalId(): ?int
    {
        $arr = explode(Utilities::TENANT_SEPARATOR, $this->ext_id);

        return isset($arr[1]) ? (int) $arr[1] : null;
    }

    public function getTransactionId(): ?int
    {
        $arr = explode(Utilities::TENANT_SEPARATOR, $this->ext_id);

        return isset($arr[2]) ? (int) $arr[2] : null;
    }
}
