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
        new OA\Property(property: 'min_amount', type: 'int', example: '0'),
        new OA\Property(property: 'max_amount', type: 'int', example: '10000000'),
        new OA\Property(property: 'min_initial_fee', type: 'int', example: '0'),
        new OA\Property(property: 'max_initial_fee', type: 'int', example: '10000000'),
    ],
)]
/**
 * use @mixin Tariff
 */
class TariffResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'is_annual' => $this->is_annual,
            'percent' => $this->percent,
            'min_amount' => $this->min_amount,
            'max_amount' => $this->max_amount,
            'min_initial_fee' => $this->min_initial_fee,
            'max_initial_fee' => $this->max_initial_fee,
        ];
    }
}
