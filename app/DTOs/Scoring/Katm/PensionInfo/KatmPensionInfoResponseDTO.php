<?php

namespace App\DTOs\Scoring\Katm\PensionInfo;

use App\DTOs\Scoring\Base\BaseScoringResponse;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'KatmPensionInfoResponseDTO',
    title: 'KatmPensionInfoResponseDTO',
    properties: [
        new OA\Property(property: 'result', type: 'string', example: '1'),
        new OA\Property(property: 'sizePension', type: 'string', example: '12321'),
        new OA\Property(property: 'comments', type: 'string', example: 'Неявляется получателем пенсии (пособия)'),
    ],
)]
class KatmPensionInfoResponseDTO extends BaseScoringResponse
{
    public function __construct(
        public string $result,
        public string $sizePension,
        public string $comments,
    ) {
    }
}
