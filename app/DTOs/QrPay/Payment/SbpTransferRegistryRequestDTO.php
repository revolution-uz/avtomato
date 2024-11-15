<?php

namespace App\DTOs\QrPay\Payment;

use App\DTOs\QrPay\Base\BaseQrPayDTO;

class SbpTransferRegistryRequestDTO extends BaseQrPayDTO
{
    public function __construct(
        public string $date,
    ) {}
}
