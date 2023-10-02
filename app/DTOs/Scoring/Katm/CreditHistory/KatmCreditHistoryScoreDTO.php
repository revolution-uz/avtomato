<?php

namespace App\DTOs\Scoring\Katm\CreditHistory;

use App\DTOs\Scoring\Base\BaseScoringResponse;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'KatmCreditHistoryScoreDTO',
    title: 'KatmCreditHistoryScoreDTO',
    properties: [
        new OA\Property(property: 'score_point', type: 'double', example: '273'),
        new OA\Property(property: 'score_class', type: 'string', example: 'C3'),
        new OA\Property(property: 'score_level', type: 'string', example: 'СРЕДНИЙ'),
        new OA\Property(property: 'score_date', type: 'string', example: '2023-09-30 17:54'),
        new OA\Property(property: 'score_version', type: 'string', example: '1.6'),
    ],
)]
class KatmCreditHistoryScoreDTO extends BaseScoringResponse
{
    public function __construct(
        public float $score_point,
        public string $score_class,
        public string $score_level,
        public string $score_date,
        public string $score_version,
    ) {
    }
}
