<?php

namespace App\Http\Resources\Installment;

use App\Models\Company\Customer;
use App\Models\Company\Installment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'InstallmentCustomersListResource',
    title: 'InstallmentCustomersList ',
    properties: [
        new OA\Property(property: 'id', type: 'int', example: '1'),
        new OA\Property(property: 'full_name', type: 'string', example: 'John Doe'),
        new OA\Property(property: 'type', type: 'string', example: 'debtor'),
    ],
)]
/**
 * @property Installment $resource
 */
class InstallmentCustomersListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $data = [
            [
                'id' => $this->resource->customer_id,
                'full_name' => $this->resource->customer->first_name . ' ' . $this->resource->customer->last_name . ' ' . $this->resource->customer->middle_name,
                'type' => trans('api.debtor'),
            ],
        ];

        /** @var Customer $guarantor */
        foreach ($this->resource->guarantors as $guarantor) {
            $data[] = [
                'id' => $guarantor->id,
                'full_name' => $guarantor->first_name . ' ' . $guarantor->last_name . ' ' . $guarantor->middle_name,
                'type' => trans('api.guarantor'),
            ];
        }

        return $data;
    }
}
