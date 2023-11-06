<?php

namespace App\DTOs\Scoring\Katm\PensionInfo;

use App\DTOs\Scoring\Base\BaseScoringResponse;
use OpenApi\Attributes as OA;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\DataCollection;

#[OA\Schema(
    schema: 'KatmPensionSizePensionDTO',
    title: 'KatmPensionSizePensionDTO',
    properties: [
        new OA\Property(property: 'data', ref: '#/components/schemas/KatmIncomeDTO'),
        new OA\Property(property: 'row', type: 'array', items: new OA\Items(ref: '#/components/schemas/KatmPensionRowDTO')),
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
