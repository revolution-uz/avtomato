<?php

namespace App\Http\Requests\Customer;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'CustomerByPassportRequest',
    title: 'Request to get customer by passport data',
    properties: [
        new OA\Property(property: 'passport_series', type: 'string', example: 'AA'),
        new OA\Property(property: 'passport_number', type: 'string', example: '1234567'),
        new OA\Property(property: 'pinfl', type: 'string', example: '12345678901234'),
    ],
)]
/**
 * @property-read string $passport_series
 * @property-read string $passport_number
 * @property-read string $pinfl
 */
class CustomerByPassportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::CUSTOMER_VIEW);
    }

    public function rules(): array
    {
        return [
            'pinfl' => [
                'nullable',
                'string',
                'size:14',
                'regex:/^[0-9]+$/',
                Rule::requiredIf(empty($this->passport_series) && empty($this->passport_number)),
            ],
            'passport_series' => [
                'nullable',
                'string',
                'size:2',
                'regex:/^[A-Z]+$/',
                Rule::requiredIf(empty($this->pinfl)),
            ],
            'passport_number' => [
                'nullable',
                'string',
                'size:7',
                'regex:/^[0-9]+$/',
                Rule::requiredIf(empty($this->pinfl)),
            ],
        ];
    }
}
