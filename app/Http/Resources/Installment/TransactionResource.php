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
        new OA\Property(property: 'branch', type: 'string', example: 'Branch name'),
        new OA\Property(property: 'branch_id', type: 'int', example: '1'),
        new OA\Property(property: 'admin', type: 'string', example: 'Admin name'),
        new OA\Property(property: 'admin_id', type: 'int', example: '1'),
        new OA\Property(property: 'customer_id', type: 'int', example: '1'),
        new OA\Property(property: 'customer', type: 'string', example: 'Customer name'),
        new OA\Property(property: 'installment_id', type: 'int', example: '1'),
        new OA\Property(property: 'amount', type: 'float', example: '1000.00'),
        new OA\Property(property: 'transaction_type_id', type: 'int', example: '1'),
        new OA\Property(property: 'transaction_type', type: 'string', example: 'Transaction type'),
        new OA\Property(property: 'updated_at', type: 'int', example: '1631610000'),
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
            'branch_name' => $this->resource->installment->branch->name,
            'branch_id' => $this->resource->installment->branch_id,
            'admin_name' => $this->resource->admin?->full_name,
            'admin_id' => $this->resource->admin_id,
            'customer_id' => $this->resource->customer_id,
            'customer_name' => $this->resource->customer->full_name,
            'installment_id' => $this->resource->installment_id,
            'ext_id' => $this->resource->external_id,
            'amount' => $this->resource->amount,
            'transaction_type_id' => $this->resource->transaction_type_id,
            'transaction_type' => $this->resource->transactionType->{'name_' . app()->getLocale()},
            'updated_at' => $this->resource->updated_at->getTimestamp(),
            'card' => $this->resource->card?->pan ?? '',
            'card_id' => $this->resource->card_id,
        ];
    }
}
