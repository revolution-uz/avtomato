<?php

namespace App\Http\Requests\Customer;

use App\Enums\Company\AddressTypeEnum;
use App\Enums\Company\PhoneTypeEnum;
use App\Enums\Company\Rbac\PermissionEnum;
use App\Enums\GenderEnum;
use App\Helpers\RbacHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'CreateCustomerRequest',
    title: 'Request to create a customer',
    properties: [
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
        new OA\Property(property: 'phones', type: 'array', items: new OA\Items(ref: '#/components/schemas/AttachPhoneRequest')),
        new OA\Property(property: 'addresses', type: 'array', items: new OA\Items(ref: '#/components/schemas/AttachAddressRequest')),
    ],
)]
/**
 * @property int $pinfl
 * @property int $tin
 * @property string $passport_series
 * @property string $passport_number
 * @property string $passport_given_date
 * @property string $passport_expiration_date
 * @property string $passport_given_place
 * @property string $passport_citizenship
 * @property string $first_name
 * @property string $last_name
 * @property string $middle_name
 * @property string $birth_date
 * @property string $birth_place
 * @property int $gender
 * @property array{"phone": int, "type": int, "comment": string} $phones
 * @property array{"type": int, "address": string, "region_id": int, "district_id": int, "mahalla_id": int} $addresses
 */
class CreateCustomerRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'pinfl' => [
                'required',
                'integer',
                'digits:14',
                'regex:/^[0-9]+$/',
                Rule::unique('customers', 'pinfl'),
            ],
            'tin' => [
                'nullable',
                'integer',
                'digits:9',
                'regex:/^[0-9]+$/',
                Rule::unique('customers', 'tin'),
            ],
            'passport_series' => [
                'required',
                'string',
                'size:2',
                'regex:/^[A-Z]+$/',
            ],
            'passport_number' => [
                'required',
                'string',
                'size:7',
                'regex:/^[0-9]+$/',
                Rule::unique('customers', 'passport_number')
                    ->where('passport_series', $this->passport_series),
            ],
            'passport_given_date' => [
                'required',
                'date',
            ],
            'passport_expiration_date' => [
                'required',
                'date',
            ],
            'passport_given_place' => [
                'nullable',
                'string',
                'max:128',
            ],
            'passport_citizenship' => [
                'nullable',
                'string',
                'max:32',
            ],
            'first_name' => [
                'required',
                'string',
                'max:32',
            ],
            'last_name' => [
                'required',
                'string',
                'max:32',
            ],
            'middle_name' => [
                'nullable',
                'string',
                'max:32',
            ],
            'birth_date' => [
                'required',
                'date',
            ],
            'birth_place' => [
                'nullable',
                'string',
                'max:32',
            ],
            'gender' => [
                'required',
                'integer',
                Rule::in(GenderEnum::values()),
            ],
            'phones' => [
                'nullable',
                'array',
            ],
            'phones.*' => 'array:phone,type,comment',
            'phones.*.phone' => [
                'required',
                'integer',
                'digits_between:9,13',
                'regex:/^[0-9]+$/',
            ],
            'phones.*.type' => [
                'required',
                'integer',
                Rule::in(PhoneTypeEnum::values()),
            ],
            'phones.*.comment' => [
                'nullable',
                'string',
                'max:128',
            ],
            'addresses' => [
                'nullable',
                'array',
            ],
            'addresses.*' => 'array:type,address,region_id,district_id,mahalla_id',
            'addresses.*.type' => [
                'required',
                'integer',
                Rule::in(AddressTypeEnum::values()),
            ],
            'addresses.*.address' => [
                'required',
                'string',
                'max:255',
            ],
            'addresses.*.region_id' => [
                'nullable',
                'integer',
                Rule::exists('regions', 'id'),
            ],
            'addresses.*.district_id' => [
                'nullable',
                'integer',
                Rule::exists('districts', 'id'),
            ],
            'addresses.*.mahalla_id' => [
                'nullable',
                'integer',
                Rule::exists('mahallas', 'id'),
            ],
            // TODO attachments
        ];
    }

    public function authorize(): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::CUSTOMER_CREATE);
    }
}
