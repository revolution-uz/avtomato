<?php

namespace App\Http\Requests\Installment;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use App\Models\Company\Tariff;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'TariffRequest',
    title: 'Request to create or update a tariff',
    properties: [
        new OA\Property(property: 'name', type: 'string', example: 'Tariff Name'),
        new OA\Property(property: 'is_annual', type: 'boolean', example: 'true'),
        new OA\Property(property: 'percent', type: 'double', example: '40'),
        new OA\Property(property: 'min_period', type: 'int', example: '3'),
        new OA\Property(property: 'max_period', type: 'int', example: '12'),
        new OA\Property(property: 'min_amount', type: 'int', example: '0'),
        new OA\Property(property: 'max_amount', type: 'int', example: '10000000'),
        new OA\Property(property: 'min_initial_fee', type: 'int', example: '0'),
        new OA\Property(property: 'max_initial_fee', type: 'int', example: '10000000'),
    ],
)]
/**
 * use @mixin Tariff
 */
class TariffRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:40',
            'is_annual' => 'required|boolean',
            'percent' => 'required|numeric',
            'min_period' => 'required|int',
            'max_period' => 'required|int',
            'min_amount' => 'required|int',
            'max_amount' => 'required|int',
            'min_initial_fee' => 'required|int',
            'max_initial_fee' => 'required|int',
        ];
    }

    public function authorize(): bool
    {
        return $this->id ?
            RbacHelper::checkPermission(PermissionEnum::TARIFF_EDIT) :
            RbacHelper::checkPermission(PermissionEnum::TARIFF_CREATE);
    }
}
