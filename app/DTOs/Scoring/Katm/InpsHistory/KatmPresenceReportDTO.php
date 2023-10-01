<?php

namespace App\DTOs\Scoring\Katm\InpsHistory;

use App\DTOs\Scoring\Base\BaseScoringResponse;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'KatmPresenceReportDTO',
    title: 'KatmPresenceReportDTO',
    properties: [
        new OA\Property(property: 'num', type: 'string', example: '1'),
        new OA\Property(property: 'report_name', type: 'string', example: 'Скоринг КИАЦ'),
        new OA\Property(property: 'presence', type: 'string', example: 'Да'),
    ],
)]
class KatmPresenceReportDTO extends BaseScoringResponse
{
    public function __construct(
        public string $num,
        public string $report_name,
        public string $presence,
    )
    {
    }
}
