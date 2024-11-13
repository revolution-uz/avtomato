<?php

namespace App\DTOs\QrPay\Terminal;

use App\DTOs\QrPay\Base\BaseQrPayDTO;

/**
 * Class SbpDeactivateTerminalRequestDTO
 *
 * @property int $terminal_id
 */
class SbpDeactivateTerminalRequestDTOBase extends BaseQrPayDTO
{
    public function __construct(
        public int $terminal_id,
    ) {}
}
