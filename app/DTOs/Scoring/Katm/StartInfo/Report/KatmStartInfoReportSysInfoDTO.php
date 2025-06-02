<?php

namespace App\DTOs\Scoring\Katm\StartInfo\Report;

use App\DTOs\Scoring\Base\BaseScoringResponse;

class KatmStartInfoReportSysInfoDTO extends BaseScoringResponse
{
    public function __construct(
        public ?string $declaration,
        public ?string $bank,
        public ?string $branch,
        public ?string $demand_id,
        public ?string $date,
        public ?string $claim_id,
        public ?string $claim_date,
        public ?string $report_type,
    ) {}
}
