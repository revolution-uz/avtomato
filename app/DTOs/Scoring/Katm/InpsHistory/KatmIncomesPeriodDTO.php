<?php

namespace App\DTOs\Scoring\Katm\InpsHistory;

use App\DTOs\Scoring\Base\BaseScoringResponse;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'KatmIncomesPeriodDTO',
    title: 'KatmIncomesPeriodDTO',
    properties: [
        new OA\Property(property: 'incomes_period_begin', type: 'string', example: '2022-10-01'),
        new OA\Property(property: 'incomes_period_end', type: 'string', example: '2023-10-01'),
        new OA\Property(property: 'incomes_all_summa', type: 'string', example: '0'),
    ],
)]
class KatmIncomesPeriodDTO extends BaseScoringResponse
{
    public function __construct(
        public string $incomes_period_begin,
        public string $incomes_period_end,
        public string $incomes_all_summa,
    ) {}
}
