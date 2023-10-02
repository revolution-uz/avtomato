<?php

namespace App\Http\Resources\Customer;

use App\Models\Company\Card;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'CardResource',
    title: 'Card object',
    properties: [
        new OA\Property(property: 'customer_id', type: 'int', example: '1'),
        new OA\Property(property: 'number', type: 'string', example: '986011******4444'),
        new OA\Property(property: 'expiry', type: 'string', example: '2**9'),
    ],
)]
/**
 * use @mixin Card
 */
class CardResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'customer_id' => $this->customer_id,
            'number' => $this->pan,
            'expiry' => $this->expiry,
            'balance' => $this->balance,
            'created_at' => $this->created_at ? (int) $this->created_at->format('U') : null,
            'updated_at' => $this->updated_at ? (int) $this->updated_at->format('U') : null,
        ];
    }
}
