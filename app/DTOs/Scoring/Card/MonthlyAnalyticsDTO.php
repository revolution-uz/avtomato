<?php

namespace App\DTOs\Scoring\Card;

use App\DTOs\Scoring\Base\BaseScoringResponse;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'MonthlyAnalyticsDTO',
    title: 'MonthlyAnalyticsDTO',
    properties: [
        new OA\Property(property: 'month', type: 'string', example: '1'),
        new OA\Property(property: 'full_amount', type: 'int', example: '33244900'),
        new OA\Property(property: 'full_amount', type: '#/components/schemas/DetailedAnalyticsInMonthDTO', example: '33244900'),
    ],
)]
class MonthlyAnalyticsDTO extends BaseScoringResponse
{
    public function __construct(
        public string $month,
        public int $full_amount,
        public DetailedAnalyticsInMonthDTO $details
    ) {
    }
}
