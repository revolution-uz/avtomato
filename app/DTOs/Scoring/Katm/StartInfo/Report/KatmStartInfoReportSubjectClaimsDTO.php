<?php

namespace App\DTOs\Scoring\Katm\StartInfo\Report;

use App\DTOs\Scoring\Base\BaseScoringResponse;

class KatmStartInfoReportSubjectClaimsDTO extends BaseScoringResponse
{
    public function __construct(
        public ?KatmStartInfoReportSubjectClaimDTO $bank_claims,
        public ?KatmStartInfoReportSubjectClaimDTO $leasing_claims,
        public ?KatmStartInfoReportSubjectClaimDTO $lombard_claims,
    ) {}
}
