<?php

namespace App\DTOs\Scoring\Katm\InpsHistory;

use App\Casts\DataCollectionCast;
use App\DTOs\Scoring\Base\BaseScoringResponse;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\DataCollection;

class KatmIncomesDTO extends BaseScoringResponse
{
    public function __construct(
        #[WithCast(DataCollectionCast::class, class: KatmIncomeDTO::class)]
        #[DataCollectionOf(KatmIncomeDTO::class)]
        public ?DataCollection $income,
    ) {}
}
