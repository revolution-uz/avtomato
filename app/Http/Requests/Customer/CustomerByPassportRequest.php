<?php

namespace App\Http\Requests\Customer;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'CustomerByPassportRequest',
    title: 'Request to get customer by passport data',
    properties: [
        new OA\Property(property: 'passport_series', type: 'string', example: 'AA'),
        new OA\Property(property: 'passport_number', type: 'int', example: '1234567'),
        new OA\Property(property: 'pinfl', type: 'int', example: '12345678901234'),
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
            'pinfl' => 'integer|digits:14|required_without:passport_series,passport_number',
            'passport_series' => 'string|max:2|min:2|required_without:pinfl|regex:/^[A-Z]+$/',
            'passport_number' => 'string|max:7|min:7|regex:/^[0-9]+$/|required_without:pinfl',
        ];
    }
}
