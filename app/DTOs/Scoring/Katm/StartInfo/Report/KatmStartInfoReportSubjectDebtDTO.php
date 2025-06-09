<?php

namespace App\DTOs\Scoring\Katm\StartInfo\Report;

use App\DTOs\Scoring\Base\BaseScoringResponse;

class KatmStartInfoReportSubjectDebtDTO extends BaseScoringResponse
{
    public function __construct(
        public ?string $org_type,
        public ?string $org_name,
        public ?string $currency,
        public ?string $all_debts,
        public ?string $curr_debts,
        public ?string $last_update,
    ) {}
}
