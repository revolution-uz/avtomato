<?php

namespace App\DTOs\Scoring\Katm\StartInfo\Report23;

use App\DTOs\Scoring\Base\BaseScoringResponse;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\DataCollection;

class KatmStartInfoReport23OpenContractsDTO extends BaseScoringResponse
{
    public function __construct(
        #[DataCollectionOf(KatmStartInfoReport23OpenContractDTO::class)]
        public ?DataCollection $open_contract,
        public ?int $all_debt_sum,
        public ?int $all_overdue_debt_sum,
        public ?int $average_monthly_payment,
    ) {}
}
