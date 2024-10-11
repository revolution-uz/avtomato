<?php

namespace App\DTOs\Royxat;

use App\DTOs\Scoring\Base\BaseScoringRequest;
use Carbon\Carbon;

class RoyxatCreditResponseDTO extends BaseScoringRequest
{
    public function __construct(
        public int $id,
        public string $credit_id,
        public int $amount,
        public int $duration,
        public string $name,
        public string $passport,
        public ?int $tin,
        public ?int $pinfl,
        public string $credit_status,
        public Carbon $contract_date,
        public int $overdue_days,
        public int $merchant_id,
        public string $merchant_name,
        public Carbon $created_at,
        public Carbon $updated_at,
    ) {}
}
