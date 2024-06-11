<?php

namespace App\Http\Resources\Installment;

use App\Models\Company\TransactionType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'TransactionTypeResource',
    title: 'TransactionType object',
    properties: [
        new OA\Property(property: 'id', type: 'int', example: '1'),
        new OA\Property(property: 'name', type: 'string', example: 'Onlayn'),
        new OA\Property(property: 'is_system', type: 'boolean', example: 'true'),
        new OA\Property(property: 'created_at', type: 'int', example: '16933424'),
        new OA\Property(property: 'updated_at', type: 'int', example: '16933424'),
    ],
)]
/**
 * @property TransactionType $resource
 */
class TransactionTypeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->{'name_' . app()->getLocale()},
            'is_system' => $this->resource->is_system,
            'created_at' => $this->resource->created_at?->format('U'),
            'updated_at' => $this->resource->updated_at?->format('U'),
        ];
    }
}
