<?php

namespace App\DTOs\Scoring\RoyxatCreditHistory;

use App\DTOs\Scoring\Base\BaseScoringRequest;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;

class RoyxatCreditHistoryLoanDTO extends BaseScoringRequest
{
    public function __construct(
        public ?int $id,
        public ?int $amount,
        #[WithCast(DateTimeInterfaceCast::class, format: 'Y-m-d\TH:i:s.u\Z')]
        public Carbon $contract_date,
        public ?int $duration,
        public ?int $max_overdue_days,
        public ?int $total_overdue_days,
        public ?int $total_overdue_amount,
        public ?string $status,
    ) {}
}
