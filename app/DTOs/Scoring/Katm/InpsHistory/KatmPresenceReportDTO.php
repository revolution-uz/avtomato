<?php

namespace App\DTOs\Scoring\Katm\InpsHistory;

use App\DTOs\Scoring\Base\BaseScoringResponse;

class KatmPresenceReportDTO extends BaseScoringResponse
{
    public function __construct(
        public string $num,
        public string $report_name,
        public string $presence,
    ) {}
}
