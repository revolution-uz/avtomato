<?php

namespace App\Http\Resources\Installment;

use App\Models\Company\Tariff;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'TariffResource',
    title: 'Tariff object',
    properties: [
        new OA\Property(property: 'id', type: 'int', example: '1'),
        new OA\Property(property: 'name', type: 'string', example: 'Tariff Name'),
        new OA\Property(property: 'is_annual', type: 'boolean', example: 'true'),
        new OA\Property(property: 'percent', type: 'double', example: '40'),
        new OA\Property(property: 'min_period', type: 'int', example: '3'),
        new OA\Property(property: 'max_period', type: 'int', example: '12'),
        new OA\Property(property: 'min_amount', type: 'int', example: '0'),
        new OA\Property(property: 'max_amount', type: 'int', example: '10000000'),
        new OA\Property(property: 'min_initial_fee', type: 'int', example: '0'),
        new OA\Property(property: 'max_initial_fee', type: 'int', example: '10000000'),
    ],
)]
/**
 * @property Tariff $resource
 */
class TariffResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'name' => $this->resource->name,
            'is_annual' => $this->resource->is_annual,
            'percent' => $this->resource->percent,
            'min_period' => $this->resource->min_period,
            'max_period' => $this->resource->max_period,
            'min_amount' => $this->resource->min_amount,
            'max_amount' => $this->resource->max_amount,
            'min_initial_fee' => $this->resource->min_initial_fee,
            'max_initial_fee' => $this->resource->max_initial_fee,
        ];
    }
}
