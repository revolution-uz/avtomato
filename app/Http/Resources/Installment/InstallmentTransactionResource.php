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
        new OA\Property(property: 'employee_id', type: 'int', example: '1'),
        new OA\Property(property: 'employee', type: 'string', example: 'John Doe'),
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
            'customer_id' => $this->resource->customer_id,
            'employee_id' => $this->resource->employee_id,
            'employee' => $this->resource->employee?->fullName() ?? 'Avtomato Bot',
            'customer' => $this->resource->customer->fullName(),
            'card_id' => $this->resource->card_id,
            'card_pan' => $this->resource->card?->pan,
            'external_id' => $this->resource->external_id,
            'transaction_type' => $this->resource->transactionType->{'name_' . app()->getLocale()},
            'amount' => $this->resource->amount,
            'created_at' => $this->resource->created_at->getTimestamp(),
        ];
    }
}
