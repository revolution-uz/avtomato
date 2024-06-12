<?php

namespace App\Http\Resources\Installment;

use App\Models\Company\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'InstallmentCustomersListItemResource',
    title: 'InstallmentCustomersListItem ',
    properties: [
        new OA\Property(property: 'id', type: 'int', example: '1'),
        new OA\Property(property: 'full_name', type: 'string', example: 'John Doe'),
        new OA\Property(property: 'type', type: 'string', example: 'debtor'),
    ],
)]
/**
 * @property Customer $resource
 */
class InstallmentCustomersListItemResource extends JsonResource
{
    public function __construct($resource, private readonly bool $isGuarantor)
    {
        parent::__construct($resource);
    }

    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'full_name' => $this->resource->full_name,
            'type' => $this->isGuarantor ? trans('api.guarantor') : trans('api.debtor'),
        ];
    }
}
