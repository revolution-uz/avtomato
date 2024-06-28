<?php

namespace App\Http\Resources\Customer;

use App\DTOs\Atmos\Card\AtmosAttachCardResponseDTO;
use App\Models\Company\Card;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'AttachCustomerCardResource',
    title: 'AttachCustomerCardResource object',
    properties: [
        new OA\Property(property: 'attachData', ref: '#/components/schemas/AtmosAttachCardResponseDTO'),
        new OA\Property(property: 'card', ref: '#/components/schemas/CardResource'),
    ],
)]
class AttachCustomerCardResource extends JsonResource
{
    public function __construct(
        public Card $card,
        public ?AtmosAttachCardResponseDTO $attachData = null,
    ) {}

    public function toArray($request): array
    {
        return [
            'attachData' => $this->attachData,
            'card' => new CardResource($this->card),
        ];
    }
}
