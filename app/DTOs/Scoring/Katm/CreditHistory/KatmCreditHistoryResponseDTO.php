<?php

namespace App\DTOs\Scoring\Katm\CreditHistory;

use App\DTOs\Scoring\Base\BaseScoringResponse;
use App\DTOs\Scoring\Katm\KatmClientDTO;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'KatmCreditHistoryResponseDTO',
    title: 'KatmCreditHistoryResponseDTO',
    properties: [
        new OA\Property(property: 'additional_info', ref: '#/components/schemas/KatmCreditHistoryAdditionalInfoDTO'),
        new OA\Property(property: 'score', ref: '#/components/schemas/KatmCreditHistoryScoreDTO'),
        new OA\Property(property: 'client', ref: '#/components/schemas/KatmClientDTO'),
    ],
)]
class KatmCreditHistoryResponseDTO extends BaseScoringResponse
{
    public function __construct(
        public KatmCreditHistoryAdditionalInfoDTO $additional_info,
        public KatmCreditHistoryScoreDTO          $score,
        public KatmClientDTO                      $client,
    )
    {
    }
}
