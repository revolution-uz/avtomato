<?php

namespace App\DTOs\QrPay\System;

use App\DTOs\QrPay\Base\BaseQrPayDTO;

/**
 * Class QrPayLoginResponseDTO
 *
 * @property string $access_token_key
 */
class QrPayLoginResponseDTO extends BaseQrPayDTO
{
    public function __construct(
        public string $access_token_key,
    ) {}
}
