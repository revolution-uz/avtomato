<?php

namespace App\Http\Resources\Customer;

use App\DTOs\PassportDataDTO;
use App\Enums\GenderEnum;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'CustomerInfoByPassportDataResource',
    title: 'Customer info by passport data',
    properties: [
        new OA\Property(property: 'pinfl', type: 'int', example: '3150319951234567'),
        new OA\Property(property: 'tin', type: 'int', example: '123123123'),
        new OA\Property(property: 'passport_series', type: 'string', example: 'AB'),
        new OA\Property(property: 'passport_number', type: 'string', example: '1234567'),
        new OA\Property(property: 'passport_given_date', type: 'string', example: '24-12-2016'),
        new OA\Property(property: 'passport_expiration_date', type: 'string', example: '24-12-2026'),
        new OA\Property(property: 'passport_given_place', type: 'string', example: 'Urganch shaxar IIB'),
        new OA\Property(property: 'passport_citizenship', type: 'string', example: 'ЎЗБЕКИСТОН'),
        new OA\Property(property: 'first_name', type: 'string', example: 'Yura'),
        new OA\Property(property: 'last_name', type: 'string', example: 'Sultonov'),
        new OA\Property(property: 'middle_name', type: 'string', example: 'Middle name'),
        new OA\Property(property: 'birth_date', type: 'string', example: '15-03-1995'),
        new OA\Property(property: 'birth_place', type: 'string', example: 'Urganch shaxri'),
        new OA\Property(property: 'gender', type: 'int', enum: [GenderEnum::MALE, GenderEnum::FEMALE], example: '1'),
        new OA\Property(property: 'address', ref: '#/components/schemas/AddressInfoResource', type: 'object'),
    ],
)]
/**
 * @property PassportDataDTO $resource
 */
class CustomerInfoByPassportDataResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'pinfl' => $this->resource->pinfl,
            'tin' => $this->resource->tin,
            'passport_series' => $this->resource->series,
            'passport_number' => $this->resource->number,
            'passport_given_date' => $this->resource->given_date->format('d-m-Y'),
            'passport_expiration_date' => $this->resource->expiration_date->format('d-m-Y'),
            'passport_given_place' => $this->resource->given_place,
            'passport_citizenship' => $this->resource->citizenship,
            'first_name' => $this->resource->name,
            'last_name' => $this->resource->sur_name,
            'middle_name' => $this->resource->patronymic_name,
            'birth_date' => $this->resource->birth_date->format('d-m-Y'),
            'birth_place' => $this->resource->birth_place,
            'gender' => GenderEnum::convertFromOilaKredit($this->resource->gender)->value, // OilaKredit gender same as our
            'address' => new AddressInfoResource($this->resource),
        ];
    }
}
