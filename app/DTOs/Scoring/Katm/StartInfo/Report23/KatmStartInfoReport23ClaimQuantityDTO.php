<?php

namespace App\DTOs\Scoring\Katm\StartInfo\Report23;

use App\DTOs\Scoring\Base\BaseScoringResponse;

class KatmStartInfoReport23ClaimQuantityDTO extends BaseScoringResponse
{
    public function __construct(
        public ?int $claims_qty,
        public ?int $rejected_claims_qty,
        public ?int $contracts_qty,
        public ?int $unresolved_claims_qty,
    ) {}
}
