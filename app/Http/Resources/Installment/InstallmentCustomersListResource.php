<?php

namespace App\Http\Resources\Installment;

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
            new InstallmentCustomersListItemResource($this->resource->customer, false),
        ];

        foreach ($this->resource->guarantors as $guarantor) {
            $data[] = new InstallmentCustomersListItemResource($guarantor, true);
        }

        return $data;
    }
}
