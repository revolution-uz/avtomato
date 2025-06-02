<?php

namespace App\DTOs\Scoring\Katm\StartInfo\Report23;

use App\DTOs\Scoring\Base\BaseScoringResponse;

class KatmStartInfoReport23DTO extends BaseScoringResponse
{
    public function __construct(
        public ?KatmStartInfoReport23SysInfoDTO $sysinfo,
        public ?KatmStartInfoReport23ClientDTO $client,
        public ?string $overview,
        public ?string $scorring,
        public ?KatmStartInfoReport23OpenContractsDTO $open_contracts,
        public ?string $claims_wo_contracts,
        public ?string $contracts,
        public ?string $contingent_liabilities,
        public ?string $credit_requests,
        public ?string $subscriptions,
        public ?string $blacklist,
        public ?KatmStartInfoReport23ClaimQuantitiesDTO $claims_qty,
    ) {}
}
