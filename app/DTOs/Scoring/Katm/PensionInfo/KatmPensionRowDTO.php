<?php

namespace App\DTOs\Scoring\Katm\PensionInfo;

use App\DTOs\Scoring\Base\BaseScoringResponse;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'KatmPensionRowDTO',
    title: 'KatmPensionRowDTO',
    properties: [
        new OA\Property(property: 'period', type: 'string', example: '2022/10'),
        new OA\Property(property: 'month', type: 'string', example: 'Oktyabr'),
        new OA\Property(property: 'summa', type: 'float', example: '1926720.03'),
    ],
)]
class KatmPensionRowDTO extends BaseScoringResponse
{
    public function __construct(
        public string $period,
        public string $month,
        public float $summa,
    ) {}
}
