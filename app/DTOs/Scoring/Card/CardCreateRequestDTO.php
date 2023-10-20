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
}
