<?php

namespace App\DTOs\Atmos;

use App\DTOs\Scoring\Base\BaseScoringRequest;
use App\Enums\External\AtmosResponseStatusEnum;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'AtmosResultDTO',
    title: 'Atmos Result DTO',
    properties: [
        new OA\Property(property: 'code', type: 'string', example: 'OK'),
        new OA\Property(property: 'description', type: 'string', example: 'Everything is ok'),
        new OA\Property(property: 'status', type: 'string', example: 'OK'),
    ],
)]
class AtmosResultDTO extends BaseScoringRequest
{
    public function __construct(
        public ?string $code,
        public ?string $description,
        public AtmosResponseStatusEnum $status,
    ) {
        $this->status = $this->getStatusAttribute();
    }

    /**
     * Get the status attribute from the code attribute.
     *
     * @return AtmosResponseStatusEnum|null
     */
    public function getStatusAttribute():  ?AtmosResponseStatusEnum
    {
        return AtmosResponseStatusEnum::tryFrom($this->code);
    }

    public function isSucces(): bool
    {
        return $this->status === AtmosResponseStatusEnum::OK;
    }
}
