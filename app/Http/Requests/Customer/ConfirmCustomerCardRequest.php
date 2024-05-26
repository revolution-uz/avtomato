<?php

namespace App\Http\Requests\Customer;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'ConfirmCustomerCardRequest',
    title: 'Request to confirm a card',
    properties: [
        new OA\Property(property: 'card_id', type: 'int', example: '1'),
        new OA\Property(property: 'transaction_id', type: 'int', example: '1'),
        new OA\Property(property: 'otp', type: 'string', example: '123456'),
    ],
)]
/**
 * @property int $card_id
 * @property int $transaction_id
 * @property string $otp
 */
class ConfirmCustomerCardRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'card_id' => [
                'required',
                'int',
                Rule::exists('cards', 'id')
                    ->where('customer_id', $this->route('id')),
            ],
            'transaction_id' => [
                'nullable',
                'int',
            ],
            'otp' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {

                    if ($this->transaction_id) {
                        return;
                    }

                    //todo:  This is a fake OTP, it should be validated against the real OTP
                    if ($value !== '123456') {
                        $fail('The ' . $attribute . ' is invalid.');
                    }
                },
            ],
        ];
    }

    public function authorize(): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::CUSTOMER_CARD_EDIT);
    }
}
