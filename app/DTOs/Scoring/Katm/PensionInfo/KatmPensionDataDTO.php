<?php

namespace App\DTOs\Scoring\Katm\PensionInfo;

use App\DTOs\Scoring\Base\BaseScoringResponse;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'KatmPensionDataDTO',
    title: 'KatmPensionDataDTO',
    properties: [
        new OA\Property(property: 'namelatin', type: 'string', example: 'Muhiba'),
        new OA\Property(property: 'address', type: 'string', example: 'BOBOATO1 12 12 12'),
        new OA\Property(property: 'summa', type: 'string', example: '1926720.03'),
        new OA\Property(property: 'patronymlatin', type: 'string', example: 'Qamarovna'),
        new OA\Property(property: 'document', type: 'string', example: 'AA1737639'),
        new OA\Property(property: 'apptype', type: 'string', example: '01'),
        new OA\Property(property: 'surnamelatin', type: 'string', example: 'Ergasheva'),
        new OA\Property(property: 'experience', type: 'string', example: '4 yil 0 oy 0 kun'),
        new OA\Property(property: 'type', type: 'string', example: '1'),
        new OA\Property(property: 'datepay', type: 'string', example: '8'),
        new OA\Property(property: 'pinpp', type: 'string', example: '42405621120018'),
        new OA\Property(property: 'datepens', type: 'string', example: '24.05.2016'),
    ],
)]
class KatmPensionDataDTO extends BaseScoringResponse
{
    public function __construct(
        public string $namelatin,
        public string $address,
        public string $summa,
        public string $patronymlatin,
        public string $document,
        public string $apptype,
        public string $surnamelatin,
        public string $experience,
        public string $type,
        public string $datepay,
        public string $pinpp,
        public string $datepens,
    ) {
    }
}
