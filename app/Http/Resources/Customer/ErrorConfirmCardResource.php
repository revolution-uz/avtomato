<?php

namespace App\Http\Resources\Customer;

use App\DTOs\Atmos\AtmosResultDTO;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ErrorConfirmCardResource',
    title: 'ErrorConfirmCardResource object',
    properties: [
        new OA\Property(property: 'attachData', ref: '#/components/schemas/AtmosResultDTO'),
        new OA\Property(property: 'card', ref: '#/components/schemas/CardResource'),
    ],
)]
class ErrorConfirmCardResource extends JsonResource
{
    public function __construct(
        public AtmosResultDTO $result,
        public CardResource $card,
    ) {}

    public function toArray($request): array
    {
        return [
            'result' => $this->result,
            'card' => $this->card,
        ];
    }
}
