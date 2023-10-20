<?php

namespace App\DTOs\Scoring\Card;

use App\DTOs\Scoring\Base\BaseScoringResponse;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'DetailedAnalyticsInMonthDTO',
    title: 'DetailedAnalyticsInMonthDTO',
    properties: [
        new OA\Property(property: 'description', type: 'string', example: 'Пополнение счета через cash in банкомат'),
        new OA\Property(property: 'type_code', type: 'string', example: '208'),
        new OA\Property(property: 'amount', type: 'int', example: '33244900'),
    ],
)]
class DetailedAnalyticsInMonthDTO extends BaseScoringResponse
{
    public function __construct(
        public string $description,
        public string $type_code,
        public int $amount,
    ) {
    }
}
