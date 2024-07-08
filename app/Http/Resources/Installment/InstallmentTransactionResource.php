<?php

namespace App\Http\Resources\Installment;

use App\Models\Company\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'InstallmentTransactionResource',
    title: 'InstallmentTransaction object',
    properties: [
        new OA\Property(property: 'id', type: 'int', example: '1'),
        new OA\Property(property: 'customer_id', type: 'int', example: '1'),
        new OA\Property(property: 'admin_id', type: 'int', example: '1'),
        new OA\Property(property: 'admin', type: 'string', example: 'John Doe'),
        new OA\Property(property: 'customer', type: 'string', example: 'John Doe'),
        new OA\Property(property: 'card_id', type: ['int', 'null'], example: '1'),
        new OA\Property(property: 'card_pan', type: ['string', 'null'], example: '8600********'),
        new OA\Property(property: 'external_id', type: 'int', example: '1'),
        new OA\Property(property: 'transaction_type', type: 'string', example: 'Installment'),
        new OA\Property(property: 'amount', type: 'double', example: '2100000'),
        new OA\Property(property: 'created_at', type: 'int', example: '16933424'),
    ],
)]
/**
 * @property Transaction $resource
 */
class InstallmentTransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'admin_name' => $this->resource->employee?->full_name,
            'admin_id' => $this->resource->admin_id,
            'customer_id' => $this->resource->customer_id,
            'customer_name' => $this->resource->customer->full_name,
            'ext_id' => $this->resource->external_id,
            'amount' => $this->resource->amount,
            'transaction_type' => $this->resource->transactionType->{'name_' . app()->getLocale()},
            'updated_at' => $this->resource->updated_at->getTimestamp(),
            'card' => $this->resource->card?->pan ?? '',
            'card_id' => $this->resource->card_id,
        ];
    }
}
