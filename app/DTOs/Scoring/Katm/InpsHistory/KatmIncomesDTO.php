<?php

namespace App\DTOs\Scoring\Katm\InpsHistory;

use App\DTOs\Scoring\Base\BaseScoringResponse;
use OpenApi\Attributes as OA;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\DataCollection;

#[OA\Schema(
    schema: 'KatmIncomesDTO',
    title: 'KatmIncomesDTO',
    properties: [
        new OA\Property(property: 'income', type: 'array', items: new OA\Items(ref: '#/components/schemas/KatmIncomeDTO')),
    ],
)]
class KatmIncomesDTO extends BaseScoringResponse
{
    public function __construct(
        #[DataCollectionOf(KatmIncomeDTO::class)]
        public ?DataCollection $income,
    ) {}
}
