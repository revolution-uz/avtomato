<?php

namespace App\DTOs\Scoring\Katm\CreditHistory;

use App\DTOs\Scoring\Base\BaseScoringResponse;

class KatmCreditHistoryScoreDTO extends BaseScoringResponse
{
    public function __construct(
        public float $score_point,
        public string $score_class,
        public string $score_level,
        public string $score_date,
        public string $score_version,
    ) {}
}
