<?php

namespace App\DTOs\QrPay\Terminal;

use App\DTOs\QrPay\Base\BaseQrPayDTO;

/**
 * Class SbpUpdateTerminalRequestDTO
 *
 * @property int $terminal_id
 * @property string $account
 * @property string $filial_code
 * @property string $inn
 * @property string $name
 * @property int $status
 */
class SbpUpdateTerminalRequestDTOBase extends BaseQrPayDTO
{
    public function __construct(
        public int $terminal_id,
        public string $account,
        public string $filial_code,
        public string $inn,
        public string $name,
        public int $status,
    ) {}
}
