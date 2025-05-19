<?php

namespace App\DTOs\Scoring\RoyxatCreditHistory;

use App\DTOs\Scoring\Base\BaseScoringRequest;

class RoyxatCreditHistoryBorrowerDTO extends BaseScoringRequest
{
    public function __construct(
        public ?string $full_name,
        public ?string $passport_id,
        public ?string $pinfl,
        public ?string $birth_date,
    ) {}
}
