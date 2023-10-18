<?php

namespace App\DTOs\Scoring\Katm\PensionInfo;

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
class KatmPensionSizePensionDTO extends BaseScoringResponse
{
    public function __construct(
        public KatmPensionDataDTO $data,
        #[DataCollectionOf(KatmPensionRowDTO::class)]
        public ?DataCollection $row,
    ) {
    }
}
