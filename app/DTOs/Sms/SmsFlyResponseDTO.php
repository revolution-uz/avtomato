<?php

namespace App\DTOs\Sms;

use App\DTOs\Scoring\Base\BaseScoringRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'SmsFlyResponseDTO',
    title: 'Sms Fly response dto',
    properties: [
        new OA\Property(property: 'message', type: 'string', example: 'Muvaffaqiyatli yetib bordi'),
    ],
)]
class SmsFlyResponseDTO extends BaseScoringRequest
{
    public function __construct(
        public string $message,
    ) {
    }
}
