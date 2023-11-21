<?php

namespace App\Http\Resources\Installment;

use App\Enums\Company\InstallmentStatusEnum;
use App\Enums\Company\InstallmentTypeEnum;
use App\Http\Resources\AttachmentResource;
use App\Http\Resources\Customer\CardResource;
use App\Http\Resources\Customer\CustomerListItemResource;
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
        new OA\Property(property: 'customer', ref: '#/components/schemas/CustomerListItemResource'),
        new OA\Property(property: 'branch_id', type: 'int', example: '1'),
        new OA\Property(property: 'branch', ref: '#/components/schemas/BranchResource', nullable: true),
        new OA\Property(property: 'tariff_id', type: 'int', example: '1'),
        new OA\Property(property: 'tariff', ref: '#/components/schemas/TariffResource'),
        new OA\Property(property: 'type', type: 'int', enum: [InstallmentTypeEnum::WITHOUT_CARD, InstallmentTypeEnum::WITH_CARD], example: InstallmentTypeEnum::WITHOUT_CARD),
        new OA\Property(property: 'amount', type: 'double', example: '2100000'),
        new OA\Property(property: 'initial_fee', type: 'double', example: '100000'),
        new OA\Property(property: 'description', type: 'string', example: 'inside the order'),
        new OA\Property(property: 'status', type: 'int', enum: [InstallmentStatusEnum::CANCELED, InstallmentStatusEnum::FROZEN, InstallmentStatusEnum::DRAFT, InstallmentStatusEnum::RUNNING, InstallmentStatusEnum::FINISHED], example: InstallmentStatusEnum::DRAFT),
        new OA\Property(property: 'sms_notify', type: 'boolean', example: 'false'),
        new OA\Property(property: 'any_card_charge', type: 'boolean', example: 'false'),
        new OA\Property(property: 'starts_in', type: 'int', example: '16933424'),
        new OA\Property(property: 'next_pay_date', type: 'int', example: '16933424'),
        new OA\Property(property: 'created_at', type: 'int', example: '16933424'),
        new OA\Property(property: 'updated_at', type: 'int', example: '16933424'),
        new OA\Property(property: 'graphs', type: 'array', items: new OA\Items(ref: '#/components/schemas/InstallmentGraphResource')),
        new OA\Property(property: 'cards', type: 'array', items: new OA\Items(ref: '#/components/schemas/CardResource')),
        new OA\Property(property: 'guarantors', type: 'array', items: new OA\Items(ref: '#/components/schemas/CustomerListItemResource')),
        new OA\Property(property: 'attachments', type: 'array', items: new OA\Items(ref: '#/components/schemas/AttachmentResource')),
        new OA\Property(property: 'comments', type: 'array', items: new OA\Items(ref: '#/components/schemas/InstallmentCommentListResource')),
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
            'customer' => $this->resource->customer,
            'cards' => CardResource::collection($this->resource->cards),
            'guarantors' => CustomerListItemResource::collection($this->resource->guarantors),
            'graphs' => InstallmentGraphResource::collection($this->resource->graphs),
            'documents' => AttachmentResource::collection($this->resource->documents),
            'images' => AttachmentResource::collection($this->resource->images),
            'comments' => InstallmentCommentListResource::collection($this->resource->comments),
            'branch_id' => $this->resource->branch_id,
            'branch' => $this->resource->branch,
            'tariff_id' => $this->resource->tariff_id,
            'tariff' => $this->resource->tariff,
            'amount' => $this->resource->amount,
            'initial_fee' => $this->resource->initial_fee,
            'description' => $this->resource->description,
            'starts_in' => $this->resource->starts_in->getTimestamp(),
            'next_pay_date' => $this->resource->next_pay_date->getTimestamp(),
            'created_at' => $this->resource->created_at->getTimestamp(),
            'updated_at' => $this->resource->updated_at->getTimestamp(),
        ];
    }
}
