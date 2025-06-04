<?php

namespace App\DTOs\Scoring\Katm\StartInfo\Report23;

use App\DTOs\Scoring\Base\BaseScoringResponse;

class KatmStartInfoReport23TotalClaimQuantityDTO extends BaseScoringResponse
{
    public function __construct(
        public ?int $all_claims_qty,
        public ?int $all_rejected_claims_qty,
        public ?int $all_contracts_qty,
        public ?int $all_unresolved_claims_qty,
    ) {}
}
