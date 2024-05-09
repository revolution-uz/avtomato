<?php

namespace App\Http\Requests\Management;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'BalanceFundConfirmRequest',
    title: 'Request to confirm balance fund payment',
    properties: [
        new OA\Property(property: 'transaction_id', type: 'int', example: '1'),
        new OA\Property(property: 'amount', type: 'int', example: '500', minimum: 500, description: 'Minimum amount is 500 uzs'),
        new OA\Property(property: 'otp', type: 'string', example: '123456'),
        new OA\Property(property: 'number', type: 'string', example: '8600123456784321'),
        new OA\Property(property: 'phone', type: 'string', example: '998991234567'),
    ],
)]
/**
 * @property int $session
 * @property string $otp
 */
class BalanceFundConfirmRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'transaction_id' => 'required|int',
            'amount' => 'required|int',
            'otp' => 'required|string',
            'number' => 'required|string',
            'phone' => 'required|string',
        ];
    }

    public function authorize(): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::BALANCE_FUND_CONFIRM);
    }
}
