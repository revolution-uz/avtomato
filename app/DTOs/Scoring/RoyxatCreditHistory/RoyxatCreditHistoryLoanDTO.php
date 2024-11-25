<?php

namespace App\DTOs\Scoring\RoyxatCreditHistory;

use App\DTOs\Scoring\Base\BaseScoringRequest;

class RoyxatCreditHistoryLoanDTO extends BaseScoringRequest
{
    public function __construct(
        public ?int $id,
        public ?int $amount,
        public ?string $contract_date,
        public ?int $duration,
        public ?int $max_overdue_days,
        public ?int $total_overdue_days,
        public ?int $total_overdue_amount,
        public ?string $status,
    ) {}
}
