<?php

namespace App\Http\Resources\Installment;

use App\Http\Resources\Common\AddressResource;
use App\Models\Company\Installment;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'InstallmentContactListResource',
    title: 'Installment Contact List Resource',
    properties: [
        new OA\Property(property: 'addresses', type: 'array', items: new OA\Items(
            properties: [
                new OA\Property(property: 'id', type: 'int', example: '1'),
                new OA\Property(property: 'type', type: 'int', example: '1'),
                new OA\Property(property: 'address', ref: 'AddressResource'),
                new OA\Property(property: 'customer_name', type: 'string', example: 'John Doe'),
                new OA\Property(property: 'customer_id', type: 'int', example: '1'),
                new OA\Property(property: 'is_guarantor', type: 'boolean', example: 'false'),
            ]
        )),

        new OA\Property(property: 'phones', type: 'array', items: new OA\Items(
            properties: [
                new OA\Property(property: 'phone', type: 'int', example: '998933993300'),
                new OA\Property(property: 'id', type: 'int', example: '1'),
                new OA\Property(property: 'type', type: 'int', example: '1'),
                new OA\Property(property: 'comment', type: 'string', example: 'comment'),
                new OA\Property(property: 'customer_name', type: 'string', example: 'John Doe'),
                new OA\Property(property: 'customer_id', type: 'int', example: '1'),
                new OA\Property(property: 'is_guarantor', type: 'boolean', example: 'false'),
            ]
        )),
    ]
)]
/**
 * @property Installment $resource
 */
class InstallmentContactResource extends JsonResource
{
    public function toArray($request)
    {
        $customer = $this->resource->customer;
        $guarantors = $this->resource->guarantors;

        $addresses = collect();
        $phones = collect();

        $collectCustomerData = function ($customer, $isGuarantor) use (&$addresses, &$phones) {
            foreach ($customer->addresses as $address) {
                $addresses->push([
                    'id' => $address->id,
                    'type' => $address->type,
                    'address' => new AddressResource($address->address),
                    'customer_name' => $customer->full_name,
                    'customer_id' => $customer->id,
                    'is_guarantor' => $isGuarantor,
                ]);
            }

            foreach ($customer->phones as $phone) {
                $phones->push([
                    'id' => $phone->id,
                    'type' => $phone->pivot->type,
                    'phone' => $phone->phone,
                    'comment' => $phone->pivot->comment,
                    'customer_name' => $customer->full_name,
                    'customer_id' => $customer->id,
                    'is_guarantor' => $isGuarantor,
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
