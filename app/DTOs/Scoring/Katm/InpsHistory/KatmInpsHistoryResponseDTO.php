<?php

namespace App\DTOs\Scoring\Katm\InpsHistory;

use App\DTOs\Scoring\Base\BaseScoringResponse;
use App\DTOs\Scoring\Katm\KatmClientDTO;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'KatmInpsHistoryResponseDTO',
    title: 'KatmInpsHistoryResponseDTO',
    properties: [
        new OA\Property(property: 'client', ref: '#/components/schemas/KatmClientDTO'),
        new OA\Property(property: 'incomes_period', ref: '#/components/schemas/KatmIncomesPeriodDTO'),
        new OA\Property(property: 'presence_reports', ref: '#/components/schemas/KatmPresenceReportsDTO'),
        new OA\Property(property: 'incomes', ref: '#/components/schemas/KatmIncomesDTO'),
    ],
)]
class KatmInpsHistoryResponseDTO extends BaseScoringResponse
{
    public function __construct(
        public KatmClientDTO $client,
        public KatmIncomesPeriodDTO $incomes_period,
        public KatmPresenceReportsDTO $presence_reports,
        public KatmIncomesDTO $incomes,
    ) {
    }
}
