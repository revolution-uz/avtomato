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
class InstallmentContactResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $guarantor_addresses = [];

        $this->resource->guarantors->map(function ($guarantor) use (&$guarantor_addresses) {
            $guarantor_addresses = [
                ...$guarantor_addresses,
                ...AddressResource::collection($guarantor->addresses),
            ];
        });

        $guarantor_phones = [];

        $this->resource->guarantors->map(function ($guarantor) use (&$guarantor_phones) {
            $guarantor_phones = [
                ...$guarantor_phones,
                ...PhoneResource::collection($guarantor->phones),
            ];
        });

        return [
            'addresses' => [
                'customer' => AddressResource::collection($this->resource->customer->addresses),
                'guarantor' => $guarantor_addresses,
            ],
            'phones' => [
                'customer' => PhoneResource::collection($this->resource->customer->phones),
                'guarantor' => $guarantor_phones,
            ],
        ];
    }
}
