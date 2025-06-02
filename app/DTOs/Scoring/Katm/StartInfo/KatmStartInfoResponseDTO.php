<?php

namespace App\DTOs\Scoring\Katm\StartInfo;

use App\DTOs\Scoring\Base\BaseScoringResponse;
use App\DTOs\Scoring\Katm\StartInfo\Report\KatmStartInfoReportDTO;
use App\DTOs\Scoring\Katm\StartInfo\Report23\KatmStartInfoReport23DTO;

class KatmStartInfoResponseDTO extends BaseScoringResponse
{
    public function __construct(
        public ?KatmStartInfoReportDTO $report,
        public ?KatmStartInfoReport23DTO $report23,
    ) {}
}
