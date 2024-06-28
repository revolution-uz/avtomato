<?php

namespace App\DTOs\Sms;

use App\DTOs\Scoring\Base\BaseScoringRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'SmsFlyResponseDTO',
    title: 'Sms Fly response dto',
    properties: [
        new OA\Property(property: 'success', type: 'bool', example: 'true'),
        new OA\Property(property: 'reason', type: 'string', example: 'SUCCESS'),
        new OA\Property(property: 'resultCode', type: 'int', example: '0'),
    ],
)]
class SmsFlyResponseDTO extends BaseScoringRequest
{
    public function __construct(
        public bool $success,
        public string $reason,
        public int $resultCode
    ) {}
}
