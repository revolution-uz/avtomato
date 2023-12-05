<?php

namespace App\Http\Resources\Installment;

use App\Enums\Company\InstallmentStatusEnum;
use App\Models\Company\InstallmentGraph;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'InstallmentGraphResource',
    title: 'InstallmentGraphResource object',
    properties: [
        new OA\Property(property: 'id', type: 'int', example: '1'),
        new OA\Property(property: 'installment_id', type: 'int', example: '1'),
        new OA\Property(property: 'amount', type: 'float', example: '12000'),
        new OA\Property(property: 'paid_amount', type: 'float', example: '0'),
        new OA\Property(property: 'overdue_days', type: 'float', example: '0'),
        new OA\Property(property: 'status', type: 'int', enum: [InstallmentStatusEnum::CANCELED, InstallmentStatusEnum::FROZEN, InstallmentStatusEnum::DRAFT, InstallmentStatusEnum::RUNNING, InstallmentStatusEnum::FINISHED], example: InstallmentStatusEnum::DRAFT),
        new OA\Property(property: 'starts_in', type: 'float', example: '01-01-2021'),
    ],
)]
/**
 * @property InstallmentGraph $resource
 */
class InstallmentGraphResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'installment_id' => $this->resource->installment_id,
            'amount' => $this->resource->amount,
            'paid_amount' => $this->resource->paid_amount,
            'overdue_days' => $this->resource->overdue_days,
            'status' => $this->resource->status,
            'starts_in' => $this->resource->starts_in,
        ];
    }
}
