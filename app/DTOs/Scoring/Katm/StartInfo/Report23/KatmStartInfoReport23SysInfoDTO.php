<?php

namespace App\DTOs\Scoring\Katm\StartInfo\Report23;

use App\DTOs\Scoring\Base\BaseScoringResponse;

class KatmStartInfoReport23SysInfoDTO extends BaseScoringResponse
{
    public function __construct(
        public ?string $report_type,
        public ?string $report_name,
        public ?string $org,
        public ?string $branch,
        public ?string $org_name,
        public ?string $user_id,
        public ?string $demand_id,
        public ?string $demand_date_time,
        public ?string $consent_id,
        public ?string $consent_date,
        public ?string $claim_id,
        public ?string $claim_date,
        public ?string $subject_type,
        public ?string $notes,
    ) {}
}
