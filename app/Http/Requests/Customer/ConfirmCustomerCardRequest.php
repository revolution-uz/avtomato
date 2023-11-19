<?php

namespace App\Http\Requests\Customer;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ConfirmCustomerCardRequest',
    title: 'Request to confirm a card',
    properties: [
        new OA\Property(property: 'session', type: 'int', example: '1'),
        new OA\Property(property: 'otp', type: 'string', example: '123456'),
    ],
)]

/**
 * @property int $card_id
 * @property int $session
 * @property string $otp
 */
class ConfirmCustomerCardRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'card_id' => 'required|int|exists:cards,id',
            'session' => 'required|int',
            'otp' => 'required|string',
        ];
    }

    public function authorize(): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::CUSTOMER_CARD_EDIT);
    }
}
