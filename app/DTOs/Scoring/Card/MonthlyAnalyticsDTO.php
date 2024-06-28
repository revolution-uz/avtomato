<?php

namespace App\DTOs\Scoring\Card;

use App\DTOs\Scoring\Base\BaseScoringResponse;
use Carbon\Carbon;
use OpenApi\Attributes as OA;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\WithCast;
use Spatie\LaravelData\Casts\DateTimeInterfaceCast;
use Spatie\LaravelData\DataCollection;

#[OA\Schema(
    schema: 'MonthlyAnalyticsDTO',
    title: 'MonthlyAnalyticsDTO',
    properties: [
        new OA\Property(property: 'month', type: 'string', example: '2023-07-25T10:04:48+00:00'),
        new OA\Property(property: 'full_amount', type: 'int', example: '33244900'),
        new OA\Property(property: 'details', type: 'array', items: new OA\Items(ref: '#/components/schemas/DetailedAnalyticsInMonthDTO'), nullable: true),
    ],
)]
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
