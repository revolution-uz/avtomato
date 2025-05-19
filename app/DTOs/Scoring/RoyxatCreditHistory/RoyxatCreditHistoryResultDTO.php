<?php

namespace App\DTOs\Scoring\RoyxatCreditHistory;

use App\DTOs\Scoring\Base\BaseScoringRequest;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\DataCollection;

class RoyxatCreditHistoryResultDTO extends BaseScoringRequest
{
    public function __construct(
        public ?RoyxatCreditHistoryBorrowerDTO $borrower,
        #[DataCollectionOf(RoyxatCreditHistoryLoanDTO::class)]
        public ?DataCollection $loans,
    ) {}
}
