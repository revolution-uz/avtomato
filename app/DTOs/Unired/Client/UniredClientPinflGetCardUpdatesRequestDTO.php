<?php

namespace App\DTOs\Unired\Client;

use App\DTOs\Unired\Base\BaseUniredDTO;

class UniredClientPinflGetCardUpdatesRequestDTO extends BaseUniredDTO
{
    public function __construct(
        public string $date_from,
        public string $date_to,
        public array $pinfls,
    ) {}
}
