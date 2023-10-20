<?php

namespace App\DTOs\Scoring\Card;

use App\DTOs\Scoring\Base\BaseScoringResponse;
use OpenApi\Attributes as OA;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\DataCollection;

#[OA\Schema(
    schema: 'CardScoringResponseDTO',
    title: 'CardScoringResponseDTO',
    properties: [
        new OA\Property(property: 'report', type: 'array', items: new OA\Items(ref: '#/components/schemas/MonthlyAnalyticsDTO')),
    ],
)]
class CardScoringResponseDTO extends BaseScoringResponse
{
    public function __construct(
        #[DataCollectionOf(MonthlyAnalyticsDTO::class)]
        public ?DataCollection $report,
    ) {
    }
}
