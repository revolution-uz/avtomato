<?php

namespace App\DTOs\QrPay\Terminal;

use App\DTOs\QrPay\Base\BaseQrPayDTO;

/**
 * Class SbpAddTerminalRequestDTO
 *
 * @property string $account
 * @property string $filial_code
 * @property string $inn
 * @property string $name
 */
class SbpAddTerminalRequestDTO extends BaseQrPayDTO
{
    public function __construct(
        public string $account,
        public string $filial_code,
        public string $inn,
        public string $name,
    ) {}
}
