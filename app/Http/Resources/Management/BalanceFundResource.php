<?php

namespace App\Http\Resources\Management;

use App\Models\Company\BalanceFund;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'BalanceFundResource',
    title: 'BalanceFund object',
    properties: [
        new OA\Property(property: 'id', type: 'int', example: '1'),
        new OA\Property(property: 'amount', type: 'int', example: '100000'),
        new OA\Property(property: 'phone_number', type: 'string', example: '+998991234567'),
        new OA\Property(property: 'external_transaction_id', type: 'string', example: '12345'),
        new OA\Property(property: 'card_number', type: 'string', example: '860031******9871'),
        new OA\Property(property: 'created_at', type: 'int', example: '121312312'),
    ],
)]
/**
 * @property BalanceFund $resource
 */
class BalanceFundResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'amount' => $this->resource->amount,
            'phone_number' => $this->resource->phone_number,
            'external_transaction_id' => $this->resource->external_transaction_id,
            'card_number' => $this->resource->card_number,
            'created_at' => $this->resource->created_at?->format('U'),
        ];
    }
}
