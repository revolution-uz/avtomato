<?php

namespace App\Http\Requests\Installment;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use App\Models\Company\InstallmentGuarantor;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'InstallmentGuarantorUpdateRequest',
    title: 'Request to update installment guarantor',
    properties: [
        new OA\Property(property: 'customer_id', type: 'int', example: '1'),
        new OA\Property(property: 'any_card_charge', type: 'boolean', example: 'false'),
    ],
)]
/**
 * use @mixin InstallmentGuarantor
 */
class InstallmentGuarantorUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'any_card_charge' => 'required|boolean',
        ];
    }

    public function authorize(): bool
    {
        return match (Route::currentRouteName()) {
            'installments.update-guarantor' => RbacHelper::checkPermission(PermissionEnum::INSTALLMENT_UPDATE_GUARANTOR),
            default => false
        };
    }
}
