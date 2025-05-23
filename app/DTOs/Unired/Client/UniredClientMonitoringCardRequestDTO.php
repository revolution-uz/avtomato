<?php

namespace App\DTOs\Unired\Client;

use App\DTOs\Unired\Base\BaseUniredDTO;

class UniredClientMonitoringCardRequestDTO extends BaseUniredDTO
{
    public function __construct(
        public string $card_number,
        public string $date_from,
        // date_to - default: today
        public int $page_number = 0,
    ) {}
}
