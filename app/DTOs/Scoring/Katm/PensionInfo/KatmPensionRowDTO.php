<?php

namespace App\DTOs\Scoring\Katm\PensionInfo;

use App\DTOs\Scoring\Base\BaseScoringResponse;

class KatmPensionRowDTO extends BaseScoringResponse
{
    public function __construct(
        public string $period,
        public string $month,
        public float $summa,
    ) {}
}
