<?php

namespace App\DTOs\QrPay;

use App\DTOs\QrPay\Base\BaseQrPayDTO;

/**
 * Class QrPayErrorResponseDTO
 *
 * @property string $message
 */
class QrPayErrorResponseDTO extends BaseQrPayDTO
{
    public function __construct(
        public ?int $code,
        public ?string $message,
    ) {}
}
