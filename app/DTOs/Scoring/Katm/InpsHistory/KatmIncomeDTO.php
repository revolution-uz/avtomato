<?php

namespace App\DTOs\Scoring\Katm\InpsHistory;

use App\DTOs\Scoring\Base\BaseScoringResponse;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'KatmIncomeDTO',
    title: 'KatmIncomeDTO',
    properties: [
        new OA\Property(property: 'num', type: 'string', example: '1'),
        new OA\Property(property: 'period', type: 'string', example: '2023-07'),
        new OA\Property(property: 'orgname', type: 'string', example: '"VORTEX SOLAR" MCHJ'),
        new OA\Property(property: 'org_inn', type: 'string', example: '310321569'),
        new OA\Property(property: 'income_summa', type: 'string', example: '6001600.00'),
        new OA\Property(property: 'inps_summa', type: 'string', example: '6820.00'),
        new OA\Property(property: 'send_date', type: 'string', example: '2022-10-01'),
    ],
)]
class KatmIncomeDTO extends BaseScoringResponse
{
    public function __construct(
        public string $num,
        public string $period,
        public string $orgname,
        public string $org_inn,
        public string $income_summa,
        public string $inps_summa,
        public string $send_date,
    ) {}
}
