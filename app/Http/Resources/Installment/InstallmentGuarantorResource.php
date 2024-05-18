<?php

namespace App\Http\Resources\Installment;

use App\Models\Company\InstallmentGuarantor;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'InstallmentGuarantorResource',
    title: 'InstallmentGuarantor object',
    properties: [
        new OA\Property(property: 'id', type: 'int', example: '1'),
        new OA\Property(property: 'installment_id', type: 'int', example: '1'),
        new OA\Property(property: 'customer_id', type: 'int', example: '1'),
        new OA\Property(property: 'first_name', type: 'string', example: 'Ali'),
        new OA\Property(property: 'last_name', type: 'string', example: 'Valiyev'),
        new OA\Property(property: 'middle_name', type: 'string', example: 'Ali'),
        new OA\Property(property: 'birth_date', type: 'string', example: '01-01-2000'),
        new OA\Property(property: 'any_card_charge', type: 'boolean', example: 'false'),
    ],
)]
/**
 * @property InstallmentGuarantor $resource
 */
class InstallmentGuarantorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'installment_id' => $this->resource->installment_id,
            'customer_id' => $this->resource->customer_id,
            'first_name' => $this->resource->customer->first_name,
            'last_name' => $this->resource->customer->last_name,
            'middle_name' => $this->resource->customer->middle_name,
            'birth_date' => $this->resource->customer->birth_date->format('d-m-Y'),
            'any_card_charge' => $this->resource->any_card_charge,
        ];
    }
}
