<?php

namespace App\DTOs\QrPay\Terminal;

use App\DTOs\QrPay\Base\BaseQrPayDTO;

/**
 * Class SbpTerminalResponseDTO
 *
 * @property int $id
 * @property string $number
 * @property string $filial_code
 * @property string $inn
 * @property string $name
 * @property string $status
 */
class SbpAccountResponseDTO extends BaseQrPayDTO
{
    public function __construct(
        public int $id,
        public string $number,
        public string $filial_code,
        public string $tax,
        public string $name,
        public string $status,
    ) {}
}
