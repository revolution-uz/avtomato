<?php

namespace App\Http\Resources\Customer;

use App\DTOs\Scoring\Card\CreateUserCardDTO;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'AttachCustomerCardResource',
    title: 'AttachCustomerCardResource object',
    properties: [
        new OA\Property(property: 'attachData', ref: '#/components/schemas/CreateUserCardDTO'),
        new OA\Property(property: 'card', ref: '#/components/schemas/CardResource'),
    ],
)]
class AttachCustomerCardResource extends JsonResource
{
    public function __construct(
        public CreateUserCardDTO $attachData,
        public CardResource $card,
    ) {
    }

    public function toArray($request): array
    {
        return [
            'attachData' => $this->attachData,
            'card' => $this->card,
        ];
    }
}
