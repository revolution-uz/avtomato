<?php

namespace App\DTOs\QrPay\System;

use App\DTOs\QrPay\Base\BaseQrPayDTO;

/**
 * Class QrPayLoginRequestDTO
 *
 * @property string $username
 * @property string $password
 * @property bool $refresh
 */
class QrPayLoginRequestDTO extends BaseQrPayDTO
{
    public function __construct(
        public string $username,
        public string $password,
        public bool $refresh = true
    ) {}
}
