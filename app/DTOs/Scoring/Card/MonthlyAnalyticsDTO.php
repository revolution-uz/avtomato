<?php

namespace App\DTOs\Scoring\Card;

use App\DTOs\Scoring\Base\BaseScoringResponse;
use Carbon\Carbon;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\DataCollection;

class MonthlyAnalyticsDTO extends BaseScoringResponse
{
    public function __construct(
        #[WithCast(DateTimeInterfaceCast::class)]
        public Carbon $month,
        public int $full_amount,
        #[DataCollectionOf(DetailedAnalyticsInMonthDTO::class)]
        public ?DataCollection $details,
    ) {}
}
