<?php

namespace App\DTOs\Unired\Client;

use App\DTOs\Unired\Base\BaseUniredDTO;

class UniredClientCardVerifyRequestDTO extends BaseUniredDTO
{
    public function __construct(
        public string $ext,
        public string $otp_code,
        public string $type,
    ) {}
}
