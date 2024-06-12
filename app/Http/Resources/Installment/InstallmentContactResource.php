<?php

namespace App\Http\Resources\Installment;

use App\Http\Resources\Common\AddressResource;
use App\Http\Resources\Common\PhoneResource;
use App\Models\Company\Installment;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'InstallmentContactListResource',
    title: 'Installment Contact List Resource',
    properties: [
        new OA\Property(property: 'addresses', type: 'array', items: new OA\Items(
            properties: [
                new OA\Property(property: 'address', ref: 'AddressResource'),
                new OA\Property(property: 'customer', ref: 'InstallmentCustomersListItemResource'),
            ]
        )),

        new OA\Property(property: 'phones', type: 'array', items: new OA\Items(
            properties: [
                new OA\Property(property: 'phone', ref: 'PhoneResource'),
                new OA\Property(property: 'customer', ref: 'InstallmentCustomersListItemResource'),
            ]
        )),
    ]
)]
/**
 * @property Installment $resource
 */
class InstallmentContactResource extends JsonResource
{
    public function toArray($request): array
    {
        $customer = $this->resource->customer;
        $guarantors = $this->resource->guarantors;

        $addresses = collect();
        $phones = collect();

        $collectCustomerData = function ($customer, $isGuarantor) use (&$addresses, &$phones) {
            foreach ($customer->addresses as $address) {
                $addresses->push([
                    'address' => new AddressResource($address),
                    'customer' => new InstallmentCustomersListItemResource($customer, $isGuarantor),
                ]);
            }

            foreach ($customer->phones as $phone) {
                $phones->push([
                    'phone' => new PhoneResource($phone),
                    'customer' => new InstallmentCustomersListItemResource($customer, $isGuarantor),
                ]);
            }
        };

        $collectCustomerData($customer, false);

        foreach ($guarantors as $guarantor) {
            $collectCustomerData($guarantor, true);
        }

        return [
            'addresses' => $addresses->values()->all(),
            'phones' => $phones->values()->all(),
        ];
    }
}
