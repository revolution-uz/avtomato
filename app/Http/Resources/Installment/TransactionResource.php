<?php

namespace App\Http\Resources\Installment;

use App\Models\Company\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'TransactionResource',
    title: 'Transaction object',
    properties: [
        new OA\Property(property: 'id', type: 'int', example: '1'),
        new OA\Property(property: 'installment_id', type: 'int', example: '1'),
        new OA\Property(property: 'customer_id', type: 'int', example: '1'),
        new OA\Property(property: 'card_id', type: 'int', example: '1'),
        new OA\Property(property: 'transaction_type_id', type: 'int', example: '100'),
        new OA\Property(property: 'amount', type: 'double', example: '2100000'),
        new OA\Property(property: 'on_created', type: 'int', example: '16933424'),
        new OA\Property(property: 'created_at', type: 'int', example: '16933424'),
        new OA\Property(property: 'updated_at', type: 'int', example: '16933424'),
    ],
)]
/**
 * @property Transaction $resource
 */
class TransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'installment_id' => $this->resource->installment_id,
            'customer_id' => $this->resource->customer_id,
            'card_id' => $this->resource->card_id,
            'transaction_type_id' => $this->resource->transaction_type_id,
            'amount' => $this->resource->amount,
            'on_created' => $this->resource->on_created ? (int) $this->resource->on_created->format('U') : null,
            'created_at' => $this->resource->created_at ? (int) $this->resource->created_at->format('U') : null,
            'updated_at' => $this->resource->updated_at ? (int) $this->resource->updated_at->format('U') : null,
        ];
    }
}
