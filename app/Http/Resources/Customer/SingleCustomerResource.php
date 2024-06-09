<?php

namespace App\Http\Resources\Customer;

use App\Enums\GenderEnum;
use App\Http\Resources\Common\AddressResource;
use App\Http\Resources\Common\PhoneResource;
use App\Models\Company\Customer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;
use OpenApi\Attributes\Items;

#[OA\Schema(
    schema: 'SingleCustomerResource',
    title: 'Single Customer Resource',
    properties: [
        new OA\Property(property: 'id', type: 'int', example: '1'),
        new OA\Property(property: 'pinfl', type: 'int', example: '12345678912345'),
        new OA\Property(property: 'tin', type: 'int', example: '123456789', nullable: true),
        new OA\Property(property: 'passport_series', type: 'string', example: 'AA'),
        new OA\Property(property: 'passport_number', type: 'string', example: '1234567'),
        new OA\Property(property: 'passport_given_date', type: 'string', format: 'date', example: '24-12-2016'),
        new OA\Property(property: 'passport_expiration_date', type: 'string', format: 'date', example: '24-12-2026'),
        new OA\Property(property: 'passport_given_place', type: 'string', example: 'Urganch shahar', nullable: true),
        new OA\Property(property: 'passport_citizenship', type: 'string', example: 'Uzbekistan', nullable: true),
        new OA\Property(property: 'first_name', type: 'string', example: 'John'),
        new OA\Property(property: 'last_name', type: 'string', example: 'Doe'),
        new OA\Property(property: 'middle_name', type: 'string', example: 'Alex', nullable: true),
        new OA\Property(property: 'birth_date', type: 'string', format: 'date', example: '24-12-1996'),
        new OA\Property(property: 'birth_place', type: 'string', example: 'Urganch shahar', nullable: true),
        new OA\Property(property: 'gender', type: 'int', enum: [GenderEnum::MALE, GenderEnum::FEMALE], example: '1'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2021-08-31T12:00:00+05:00'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2021-08-31T12:00:00+05:00'),
        new OA\Property(property: 'cards', type: 'array', items: new Items(ref: '#/components/schemas/CardResource')),
        new OA\Property(property: 'phones', type: 'array', items: new Items(ref: '#/components/schemas/PhoneResource')),
        new OA\Property(property: 'addresses', type: 'array', items: new Items(ref: '#/components/schemas/AddressResource')),
    ],
)]
/**
 * @property Customer $resource
 */
class SingleCustomerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'pinfl' => $this->resource->pinfl,
            'tin' => $this->resource->tin,
            'passport_series' => $this->resource->passport_series,
            'passport_number' => $this->resource->passport_number,
            'passport_given_date' => $this->resource->passport_given_date->format('d-m-Y'),
            'passport_expiration_date' => $this->resource->passport_expiration_date->format('d-m-Y'),
            'passport_given_place' => $this->resource->passport_given_place,
            'passport_citizenship' => $this->resource->passport_citizenship,
            'first_name' => $this->resource->first_name,
            'last_name' => $this->resource->last_name,
            'middle_name' => $this->resource->middle_name,
            'birth_date' => $this->resource->birth_date->format('d-m-Y'),
            'birth_place' => $this->resource->birth_place,
            'gender' => $this->resource->gender,
            'created_at' => $this->resource->created_at?->format('U'),
            'updated_at' => $this->resource->created_at?->format('U'),
            'cards' => CardResource::collection($this->resource->cards),
            'phones' => PhoneResource::collection($this->resource->phones),
            'addresses' => AddressResource::collection($this->resource->addresses),
        ];
    }
}
