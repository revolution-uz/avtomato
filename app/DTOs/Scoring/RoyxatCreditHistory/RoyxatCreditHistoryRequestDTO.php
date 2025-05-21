<?php

namespace App\DTOs\Scoring\RoyxatCreditHistory;

use App\DTOs\Scoring\Base\BaseScoringRequest;

class RoyxatCreditHistoryRequestDTO extends BaseScoringRequest
{
    public function __construct(
        public ?string $passport_id,
        public ?string $pinfl,
    ) {}
}
