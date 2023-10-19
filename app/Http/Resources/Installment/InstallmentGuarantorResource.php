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
        new OA\Property(property: 'installment_id', type: 'int', example: '1'),
        new OA\Property(property: 'installment', ref: '#/components/schemas/InstallmentResource'),
        new OA\Property(property: 'customer_id', type: 'int', example: '1'),
        new OA\Property(property: 'customer', ref: '#/components/schemas/CustomerResource'),
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
            'installment_id' => $this->resource->installment_id,
            'installment' => $this->resource->installment,
            'customer_id' => $this->resource->customer_id,
            'customer' => $this->resource->customer,
            'any_card_charge' => $this->resource->any_card_charge,
        ];
    }
}
