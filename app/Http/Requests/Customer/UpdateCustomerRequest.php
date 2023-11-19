<?php

namespace App\Http\Requests\Customer;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Enums\GenderEnum;
use App\Helpers\RbacHelper;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'UpdateCustomerRequest',
    title: 'Request to update a customer',
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
 */
class UpdateCustomerRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'pinfl' => 'required|integer|digits:14',
            'tin' => 'nullable|integer|digits:9',
            'passport_series' => 'required|string|max:2|min:2',
            'passport_number' => 'required|string|max:7|min:7|regex:/^[0-9]+$/',
            'passport_given_date' => 'required|date',
            'passport_expiration_date' => 'required|date',
            'passport_given_place' => 'nullable|string|max:128',
            'passport_citizenship' => 'nullable|string|max:32',
            'first_name' => 'required|string|max:32',
            'last_name' => 'required|string|max:32',
            'middle_name' => 'nullable|string|max:32',
            'birth_date' => 'required|date',
            'birth_place' => 'nullable|string|max:32',
            'gender' => 'required|int|in:' . implode(',', GenderEnum::values()),
        ];
    }

    public function authorize(): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::CUSTOMER_CREATE);
    }
}
