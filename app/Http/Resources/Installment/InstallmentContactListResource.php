<?php

namespace App\Http\Resources\Installment;

use App\Http\Resources\Common\AddressResource;
use App\Http\Resources\Common\PhoneResource;
use App\Models\Company\Installment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'InstallmentContactListResource',
    title: 'Installment Contact List Resource',
    properties: [
        new OA\Property(property: 'addresses', properties: [
            new OA\Property(property: 'customer', type: 'array', items: new OA\Items(ref: '#/components/schemas/AddressResource')),
            new OA\Property(property: 'guarantor', type: 'array', items: new OA\Items(ref: '#/components/schemas/AddressResource')),
        ]),
        new OA\Property(property: 'phones', properties: [
            new OA\Property(property: 'customer', type: 'array', items: new OA\Items(ref: '#/components/schemas/PhoneResource')),
            new OA\Property(property: 'guarantor', type: 'array', items: new OA\Items(ref: '#/components/schemas/PhoneResource')),
        ]),
    ]
)]
/**
 * @property Installment $resource
 */
class InstallmentContactListResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'addresses' => [
                'customer' => AddressResource::collection($this->resource->customer->addresses),
                'guarantor' => $this->resource->guarantors->map(function ($guarantor) {
                    return AddressResource::collection($guarantor->addresses);
                }),
            ],
            'phones' => [
                'customer' => PhoneResource::collection($this->resource->customer->phones),
                'guarantor' => $this->resource->guarantors->map(function ($guarantor) {
                    return PhoneResource::collection($guarantor->phones);
                }),
            ],
        ];
    }
}
