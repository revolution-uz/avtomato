<?php

namespace App\DTOs\Scoring\Katm\StartInfo\Report;

use App\DTOs\Scoring\Base\BaseScoringResponse;

class KatmStartInfoReportSubjectClaimDTO extends BaseScoringResponse
{
    public function __construct(
        public ?string $org_type,
        public ?int $claims_qty,
        public ?int $rejected_qty,
        public ?int $granted_qty,
    ) {}
}
