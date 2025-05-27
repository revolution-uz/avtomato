<?php

namespace App\DTOs\QrPay\Payment;

use App\DTOs\QrPay\Base\BaseQrPayDTO;

/**
 * Class SbpTransferDTO
 *
 * @property SbpTransferResponseDTO $transfer
 */
class SbpTransferDTO extends BaseQrPayDTO
{
    public function __construct(
        public ?SbpTransferResponseDTO $transfer,
    ) {}
}
