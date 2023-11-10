<?php

namespace App\Http\Requests\Installment;

use App\Enums\Company\FeatureEnum;
use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use App\Models\Company\Feature;
use App\Models\Company\Installment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\RequiredIf;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'InstallmentRequest',
    title: 'Request to create or update a installment',
    properties: [
        new OA\Property(property: 'customer_id', type: 'int', example: '1'),
        new OA\Property(property: 'branch_id', type: 'int', example: '1'),
        new OA\Property(property: 'tariff_id', type: 'int', example: '1'),
        new OA\Property(property: 'period', type: 'int', example: '12'),
        new OA\Property(property: 'amount', type: 'double', example: '2100000'),
        new OA\Property(property: 'prepayment', type: 'double', example: '100000'),
        new OA\Property(property: 'description', type: 'string', example: 'inside the order'),
        new OA\Property(property: 'sms_notify', type: 'boolean', example: 'false'),
        new OA\Property(property: 'any_card_charge', type: 'boolean', example: 'false'),
        new OA\Property(property: 'starts_in', type: 'int', example: '16933424'),
    ],
)]
/**
 * use @mixin Installment
 */
class InstallmentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'customer_id' => 'required|int',
            'branch_id' => ['required', [new RequiredIf(Feature::isFeatureActive(FeatureEnum::MULTI_BRANCH))]],
            'tariff_id' => 'required|int',
            'period' => 'required|int',
            'amount' => 'required|numeric',
            'prepayment' => 'required|numeric',
            'description' => 'required|string',
            'sms_notify' => 'required|boolean',
            'any_card_charge' => 'required|boolean',
            'starts_in' => 'required|boolean',
        ];
    }

    public function authorize(): bool
    {
        return $this->id ?
            RbacHelper::checkPermission(PermissionEnum::INSTALLMENT_EDIT) :
            RbacHelper::checkPermission(PermissionEnum::INSTALLMENT_CREATE);
    }
}
