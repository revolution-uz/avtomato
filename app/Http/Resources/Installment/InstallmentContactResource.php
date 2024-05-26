<?php

namespace App\Http\Resources\Installment;

use App\Models\Company\Address;
use App\Models\Company\Customer;
use App\Models\Company\Installment;
use App\Models\Company\Phone;
use Illuminate\Http\Request;
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
                new OA\Property(property: 'address', type: 'string', example: 'Al-Xorazmiy ko\'chasi 1-uy'),
                new OA\Property(property: 'region', type: 'string', example: 'Xorazm viloyati'),
                new OA\Property(property: 'district', type: 'string', example: 'Urganch tumani'),
                new OA\Property(property: 'mahalla', type: 'string', example: 'Olmazor mahallasi'),
                new OA\Property(property: 'customer_name', type: 'string', example: 'John Doe (Debtor)'),
                new OA\Property(property: 'customer_id', type: 'int', example: '1'),
            ]
        )),

        new OA\Property(property: 'phones', type: 'array', items: new OA\Items(
            properties: [
                new OA\Property(property: 'phone', type: 'int', example: '998933993300'),
                new OA\Property(property: 'id', type: 'int', example: '1'),
                new OA\Property(property: 'type', type: 'int', example: '1'),
                new OA\Property(property: 'comment', type: 'string', example: 'comment'),
                new OA\Property(property: 'customer_name', type: 'string', example: 'John Doe (Debtor)'),
                new OA\Property(property: 'customer_id', type: 'int', example: '1'),
            ]
        )),
    ]
)]
/**
 * @property Installment $resource
 */
class InstallmentContactResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $addresses = [];
        $phones = [];
        $this->resource->customer->addresses->map(function ($address) use (&$addresses) {
            $addresses[] = $this->addressData($address, $this->resource->customer);
        });
        $this->resource->customer->phones->map(function ($phone) use (&$phones) {
            $phones[] = $this->phoneData($phone, $this->resource->customer);
        });

        $this->resource->guarantors->map(function ($guarantor) use (&$addresses, &$phones) {
            $guarantor->addresses->map(function ($address) use (&$addresses, $guarantor) {
                $addresses[] = $this->addressData($address, $guarantor, true);
            });
            $guarantor->phones->map(function ($phone) use (&$phones, $guarantor) {
                $phones[] = $this->phoneData($phone, $guarantor, true);
            });
        });

        return [
            'addresses' => $addresses,
            'phones' => $phones,
        ];
    }

    private function addressData(Address $address, Customer $customer, bool $isGuarantor = false): array
    {
        return [
            'id' => $address->id,
            'type' => $address->type,
            'address' => $address->address,
            'region' => $address->region?->{'name_' . app()->getLocale()},
            'district' => $address->district?->{'name_' . app()->getLocale()},
            'mahalla' => $address->mahalla?->{'name_' . app()->getLocale()},
            'customer_name' => $customer->fullName() . ' (' . ($isGuarantor ? trans('api.guarantor') : trans('api.debtor')) . ')',
            'customer_id' => $customer->id,
        ];
    }

    private function phoneData(Phone $phone, Customer $customer, bool $isGuarantor = false): array
    {
        return [
            'phone' => $phone->phone,
            'id' => $phone->pivot->id,
            'type' => $phone->pivot->type,
            'comment' => $phone->pivot->comment,
            'customer_name' => $customer->fullName() . ' (' . ($isGuarantor ? trans('api.guarantor') : trans('api.debtor')) . ')',
            'customer_id' => $customer->id,
        ];
    }
}
