<?php

namespace App\DTOs\Scoring\UzumNasiya;

use App\DTOs\Scoring\Base\BaseScoringResponse;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\DataCollection;

class UzumNasiyaLimitResponseDTO extends BaseScoringResponse
{
    public function __construct(
        public ?int $status,
        public ?int $buyer_id,
        #[DataCollectionOf(UzumNasiyaAvailablePeriodResponseDTO::class)]
        public ?DataCollection $available_periods,
        public ?string $balance,
        public ?bool $has_overdue_contracts
    ) {}
}
