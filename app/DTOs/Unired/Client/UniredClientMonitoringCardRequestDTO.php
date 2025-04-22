<?php

namespace App\DTOs\Unired\Client;

use App\DTOs\Unired\Base\BaseUniredDTO;

class UniredClientMonitoringCardRequestDTO extends BaseUniredDTO
{
    public function __construct(
        public string $card_number,
        public string $date_from,
        public ?string $date_to, // default is today
    ) {}
}
