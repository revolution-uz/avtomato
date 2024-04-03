<?php

namespace App\Http\Requests\Customer;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'GetCustomerInfoByPassportRequest',
    title: 'Request to get customer info by passport data',
    properties: [
        new OA\Property(property: 'passport_series', type: 'string', example: 'AA'),
        new OA\Property(property: 'passport_number', type: 'string', example: '1234567'),
        new OA\Property(property: 'birth_date', type: 'date', example: '23-12-1999'),
    ],
)]
/**
 * @property string $passport_series
 * @property string $passport_number
 * @property string $birth_date
 */
class GetCustomerInfoByPassportRequest extends FormRequest
{
    public function rules(): array
    {
        return [
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
            ],
            'birth_date' => [
                'required',
                'date',
                'date_format:d-m-Y',
            ],
        ];
    }

    public function authorize(): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::CUSTOMER_INFO_BY_PASSPORT);
    }
}
