<?php

namespace App\DTOs\Scoring\Katm\InpsHistory;

use App\DTOs\Scoring\Base\BaseScoringResponse;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\DataCollection;

class KatmIncomesDTO extends BaseScoringResponse
{
    public function __construct(
        #[DataCollectionOf(KatmIncomeDTO::class)]
        public ?DataCollection $income,
    ) {}
}
