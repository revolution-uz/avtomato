<?php

namespace App\Http\Resources\Installment;

use App\Enums\Company\InstallmentStatusEnum;
use App\Enums\Company\InstallmentTypeEnum;
use App\Http\Resources\Customer\SingleCustomerResource;
use App\Http\Resources\Management\BranchResource;
use App\Http\Resources\User\UserListResource;
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
        new OA\Property(property: 'status', type: 'int', enum: InstallmentStatusEnum::class, example: InstallmentStatusEnum::DRAFT),
        new OA\Property(property: 'customer', ref: '#/components/schemas/SingleCustomerResource'),
        new OA\Property(property: 'branch_id', type: 'int', example: '1'),
        new OA\Property(property: 'branch', ref: '#/components/schemas/BranchResource', nullable: true),
        new OA\Property(property: 'user_id', type: 'int', example: '1'),
        new OA\Property(property: 'user', ref: '#/components/schemas/UserListResource', nullable: true),
        new OA\Property(property: 'tariff_id', type: 'int', example: '1'),
        new OA\Property(property: 'tariff', ref: '#/components/schemas/TariffResource'),
        new OA\Property(property: 'period', type: 'int', example: '12'),
        new OA\Property(property: 'type', type: 'int', enum: InstallmentTypeEnum::class, example: InstallmentTypeEnum::WITHOUT_CARD),
        new OA\Property(property: 'amount', type: 'double', example: '2100000'),
        new OA\Property(property: 'initial_fee', type: 'double', example: '100000'),
        new OA\Property(property: 'auto_pay', type: 'boolean', example: 'true'),
        new OA\Property(property: 'auto_pay_by_pinfl', type: 'boolean', example: 'false'),
        new OA\Property(property: 'description', type: 'string', example: 'inside the order'),
        new OA\Property(property: 'sms_notify', type: 'boolean', example: 'false'),
        new OA\Property(property: 'any_card_charge', type: 'boolean', example: 'false'),
        new OA\Property(property: 'starts_in', type: 'int', example: '16933424'),
        new OA\Property(property: 'next_pay_date', type: 'int', example: '16933424'),
        new OA\Property(property: 'created_at', type: 'int', example: '16933424'),
        new OA\Property(property: 'updated_at', type: 'int', example: '16933424'),
        new OA\Property(property: 'graphs', type: 'array', items: new OA\Items(ref: '#/components/schemas/InstallmentGraphResource')),
        new OA\Property(property: 'canceled_at', type: 'int', example: '16933424'),
        new OA\Property(property: 'cancellation_details', type: 'string', example: 'cause of cancellation'),
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
            'status' => $this->resource->status,
            'customer' => new SingleCustomerResource($this->resource->customer),
            'graphs' => InstallmentGraphResource::collection($this->resource->graphs),
            'branch_id' => $this->resource->branch_id,
            'branch' => new BranchResource($this->resource->branch),
            'user_id' => $this->resource->user_id,
            'user' => new UserListResource($this->resource->user),
            'tariff_id' => $this->resource->tariff_id,
            'tariff' => new TariffResource($this->resource->tariff),
            'period' => $this->resource->period,
            'amount' => $this->resource->amount,
            'initial_fee' => $this->resource->initial_fee,
            'auto_pay' => $this->resource->auto_pay,
            'auto_pay_by_pinfl' => $this->resource->any_card_charge,
            'description' => $this->resource->description,
            'starts_in' => $this->resource->starts_in->getTimestamp(),
            'next_pay_date' => $this->resource->next_pay_date ? $this->resource->next_pay_date->getTimestamp() : null,
            'created_at' => $this->resource->created_at->getTimestamp(),
            'updated_at' => $this->resource->updated_at->getTimestamp(),
            'canceled_at' => $this->resource->canceled_at ? $this->resource->canceled_at->getTimestamp() : null,
            'cancellation_details' => $this->resource->cancellation_details,
        ];
    }
}
