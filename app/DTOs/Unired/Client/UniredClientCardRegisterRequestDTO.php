<?php

namespace App\DTOs\Unired\Client;

use App\DTOs\Unired\Base\BaseUniredDTO;

class UniredClientCardRegisterRequestDTO extends BaseUniredDTO
{
    public function __construct(
        public string $card_number,
        public string $expire,
        public ?string $phone, // required only humo cards
        public string $type,
    ) {}
}
