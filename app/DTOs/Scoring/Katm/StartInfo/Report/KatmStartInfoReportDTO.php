<?php

namespace App\DTOs\Scoring\Katm\StartInfo\Report;

use App\DTOs\Scoring\Base\BaseScoringResponse;

class KatmStartInfoReportDTO extends BaseScoringResponse
{
    public function __construct(
        public ?KatmStartInfoReportSysInfoDTO $sysinfo,
        public ?KatmStartInfoReportClientDTO $client,
        public ?KatmStartInfoReportSubjectClaimsDTO $subject_claims,
        public ?float $subject_debts,
        public ?float $average_monthly_payment,
    ) {}
}
