<?php

namespace App\DTOs\Unired\Client;

use App\DTOs\Unired\Base\BaseUniredDTO;

class UniredRegisterCardResponseDTO extends BaseUniredDTO
{
    public function __construct(
        public string $ext,
        public string $phone_mask,
        public string $type,
    ) {}
}
