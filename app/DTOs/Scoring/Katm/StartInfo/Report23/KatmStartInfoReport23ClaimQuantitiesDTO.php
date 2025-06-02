<?php

namespace App\DTOs\Scoring\Katm\StartInfo\Report23;

use App\DTOs\Scoring\Base\BaseScoringResponse;

class KatmStartInfoReport23ClaimQuantitiesDTO extends BaseScoringResponse
{
    public function __construct(
        public ?KatmStartInfoReport23ClaimQuantityDTO $bank_claims_qty,
        public ?KatmStartInfoReport23ClaimQuantityDTO $leasing_claims_qty,
        public ?KatmStartInfoReport23ClaimQuantityDTO $retail_claims_qty,
        public ?KatmStartInfoReport23ClaimQuantityDTO $lombard_claims_qty,
        public ?KatmStartInfoReport23ClaimQuantityDTO $mko_claims_qty,
        public ?KatmStartInfoReport23ClaimQuantityDTO $insurance_claims_qty,
        public ?KatmStartInfoReport23TotalClaimQuantityDTO $total_claims_qty,
    ) {}
}
