<?php

namespace App\Http\Requests\Management;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ConfirmSubscriptionPaymentRequest',
    title: 'Request to confirm subscription payment',
    properties: [
        new OA\Property(property: 'session', type: 'int', example: '1'),
        new OA\Property(property: 'otp', type: 'string', example: '123456'),
    ],
)]

/**
 * @property int $session
 * @property string $otp
 */
class ConfirmSubscriptionPaymentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'session' => 'required|int',
            'otp' => 'required|string',
        ];
    }

    public function authorize(): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::CUSTOMER_CARD_EDIT); // so'rab o'zgartirish garak
    }
}
