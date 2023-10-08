<?php

namespace App\Http\Resources\Installment;

use App\Models\Company\Installment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'InstallmentResource',
    title: 'Installment object',
    properties: [
        new OA\Property(property: 'id', type: 'int', example: '1'),
        new OA\Property(property: 'customer_id', type: 'int', example: '1'),
        new OA\Property(property: 'branch_id', type: 'int', example: '1'),
        new OA\Property(property: 'tariff_id', type: 'int', example: '1'),
        new OA\Property(property: 'amount', type: 'double', example: '2100000'),
        new OA\Property(property: 'description', type: 'string', example: 'inside the order'),
        new OA\Property(property: 'sms_notify', type: 'boolean', example: 'false'),
        new OA\Property(property: 'auto_charge', type: 'boolean', example: 'true'),
        new OA\Property(property: 'any_card_charge', type: 'boolean', example: 'false'),
        new OA\Property(property: 'starts_in', type: 'int', example: '16933424'),
        new OA\Property(property: 'next_pay_date', type: 'int', example: '16933424'),
        new OA\Property(property: 'created_at', type: 'int', example: '16933424'),
        new OA\Property(property: 'updated_at', type: 'int', example: '16933424'),
    ],
)]
/**
 * @property Installment $resource
 */
class InstallmentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'customer_id' => $this->resource->customer_id,
            'branch_id' => $this->resource->branch_id,
            'tariff_id' => $this->resource->tariff_id,
            'amount' => $this->resource->amount,
            'description' => $this->resource->description,
            'sms_notify' => $this->resource->sms_notify,
            'auto_charge' => $this->resource->auto_charge,
            'any_card_charge' => $this->resource->any_card_charge,
            'starts_in' => $this->resource->starts_in ? (int) $this->resource->starts_in->format('U') : null,
            'next_pay_date' => $this->resource->next_pay_date ? (int) $this->resource->next_pay_date->format('U') : null,
            'created_at' => $this->resource->created_at ? (int) $this->resource->created_at->format('U') : null,
            'updated_at' => $this->resource->updated_at ? (int) $this->resource->updated_at->format('U') : null,
        ];
    }
}
