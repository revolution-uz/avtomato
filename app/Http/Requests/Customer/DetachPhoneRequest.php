<?php

namespace App\Http\Requests\Customer;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use App\Rules\PhoneRule;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'DetachPhoneRequest',
    title: 'Request to detach a phone number from customer',
    properties: [
        new OA\Property(property: 'phone', type: 'int', example: '998933993300'),
    ],
)]
/**
 * @property int $phone
 * @property int $type
 * @property string|null $comment
 */
class DetachPhoneRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'phone' => ['distinct', 'required', 'int', new PhoneRule],
        ];
    }

    public function authorize(): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::CUSTOMER_EDIT);
    }
}
