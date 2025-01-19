<?php

namespace App\DTOs\Scoring\RoyxatCreditHistory;

use App\DTOs\Scoring\Base\BaseScoringRequest;

class RoyxatCreditHistoryInputDTO extends BaseScoringRequest
{
    public function __construct(
        public ?string $pinfl,
        public ?string $passport_id,
    ) {}
}
