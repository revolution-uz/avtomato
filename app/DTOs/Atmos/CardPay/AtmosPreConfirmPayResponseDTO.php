<?php

namespace App\DTOs\Atmos\CardPay;

use App\DTOs\Atmos\AtmosResultDTO;
use App\DTOs\Scoring\Base\BaseScoringRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'AtmosPreConfirmPayResponseDTO',
    title: 'AtmosPreConfirmPayResponseDTO',
    properties: [
        new OA\Property(property: 'result', ref: '#/components/schemas/AtmosResultDTO'),
    ],
)]
class AtmosPreConfirmPayResponseDTO extends BaseScoringRequest
{
    public function __construct(
        public AtmosResultDTO $result,
    ) {
    }
}
