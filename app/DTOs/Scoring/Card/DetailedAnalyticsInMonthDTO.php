<?php

namespace App\DTOs\Scoring\Card;

use App\DTOs\Scoring\Base\BaseScoringResponse;

class DetailedAnalyticsInMonthDTO extends BaseScoringResponse
{
    public function __construct(
        public string $description,
        public string $type_code,
        public int $amount,
    ) {}
}
