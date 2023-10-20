<?php

namespace App\DTOs\Scoring\Card;

use App\DTOs\Scoring\Base\BaseScoringRequest;

class CardCreateRequestDTO extends BaseScoringRequest
{
    public function __construct(
        public string $number,
        public string $expiry,
    ) {
    }

    public function toArray(): array
    {
        return [
            'number' => $this->number,
            'expiry' => $this->expiry,
        ];
    }
}
