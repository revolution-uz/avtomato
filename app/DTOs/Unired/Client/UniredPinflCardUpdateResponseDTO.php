<?php

namespace App\DTOs\Unired\Client;

use App\DTOs\Unired\Base\BaseUniredDTO;

class UniredPinflCardUpdateResponseDTO extends BaseUniredDTO
{
    public function __construct(
        public string $pinfl,
        public string $type,
        public string $owner,
        public string $status,
        public string $processed_at,
        public int $cards_count,
        public int $created_by,
        public ?string $comment,
        public string $created_at,
    ) {}
}
