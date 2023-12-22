<?php

namespace App\Http\Resources\Customer;

use App\Enums\Company\CardStatusEnum;
use App\Models\Company\Card;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'CardResource',
    title: 'Card object',
    properties: [
        new OA\Property(property: 'customer_id', type: 'int', example: '1'),
        new OA\Property(property: 'customer', ref: '#/components/schemas/CustomerListItemResource'),
        new OA\Property(property: 'number', type: 'string', example: '986011******4444'),
        new OA\Property(property: 'expiry', type: 'string', example: '2**9'),
        new OA\Property(property: 'phone', type: 'string', example: '+998901234567'),
        new OA\Property(property: 'status', type: 'int', enum: [CardStatusEnum::BLOCKED, CardStatusEnum::INACTIVE, CardStatusEnum::ACTIVE], example: CardStatusEnum::ACTIVE),
        new OA\Property(property: 'balance', type: 'double', example: '0'),
    ],
)]
/**
 * @property Card $resource
 */
class CardResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'customer_id' => $this->resource->customer_id,
            'customer' => $this->resource->customer,
            'number' => $this->resource->pan,
            'expiry' => $this->resource->expiry,
            'phone' => $this->resource->phone,
            'status' => $this->resource->status,
            'balance' => $this->resource->balance,
            'created_at' => $this->resource->created_at ? $this->resource->created_at->getTimestamp() : null,
            'updated_at' => $this->resource->updated_at ? $this->resource->updated_at->getTimestamp() : null,
        ];
    }
}
