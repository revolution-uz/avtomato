<?php

namespace App\Http\Requests\Customer;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use App\Rules\PhoneRule;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'DetachPhonesRequest',
    title: 'Request to detach a phone number from customer',
    properties: [
        new OA\Property(property: 'phones', type: 'array', items: new OA\Items(type: 'int'), example: ['998933993300', '998901234567']),
    ],
)]
/**
 * @property array $phones
 */
class DetachPhonesRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'phones' => ['distinct', 'required', 'array'],
            'phones.*' => ['int', new PhoneRule],
        ];
    }

    public function authorize(): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::CUSTOMER_EDIT);
    }
}
