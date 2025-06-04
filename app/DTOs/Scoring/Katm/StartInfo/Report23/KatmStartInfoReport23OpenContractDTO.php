<?php

namespace App\DTOs\Scoring\Katm\StartInfo\Report23;

use App\DTOs\Scoring\Base\BaseScoringResponse;

class KatmStartInfoReport23OpenContractDTO extends BaseScoringResponse
{
    public function __construct(
        public ?string $org,
        public ?string $branch,
        public ?string $org_name,
        public ?string $contract_id,
        public ?string $currency,
        public ?int $total_debt_sum,
        public ?int $overdue_debt_sum,
        public ?int $monthly_average_payment,
    ) {}
}
