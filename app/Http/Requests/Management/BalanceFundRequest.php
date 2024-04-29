<?php

namespace App\Http\Requests\Management;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'BalanceFundRequest',
    title: 'Request to fund balance in installment',
    properties: [
        new OA\Property(property: 'number', type: 'string', example: '8600312912345678'),
        new OA\Property(property: 'expiry', type: 'string', example: '2407'),
    ],
)]
/**
 * @property string number
 * @property string expiry
 */
class BalanceFundRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'number' => 'required|string',
            'expiry' => 'required|string',
        ];
    }

    public function authorize(): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::BALANCE_FUND_CREATE);
    }
}
