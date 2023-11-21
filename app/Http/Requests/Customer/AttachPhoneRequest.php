<?php

namespace App\Http\Requests\Customer;

use App\Enums\Company\PhoneTypeEnum;
use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use App\Rules\PhoneRule;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'AttachPhoneRequest',
    title: 'Request to create a phone number',
    properties: [
        new OA\Property(property: 'phone', type: 'int', example: '998933993300'),
        new OA\Property(property: 'type', type: 'int', enum: [PhoneTypeEnum::PRIVATE, PhoneTypeEnum::WORK, PhoneTypeEnum::RELATIVE], example: '1'),
        new OA\Property(property: 'comment', type: 'string', example: 'Soat 9 dan 18 gacha qo\'ng\'iroq qilishingiz mumkin', nullable: true),
    ],
)]
/**
 * @property int $phone
 * @property int $type
 * @property string|null $comment
 */
class AttachPhoneRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'phone' => ['distinct', 'required', 'int', new PhoneRule],
            'type' => 'required|int|in:' . implode(',', PhoneTypeEnum::values()),
            'comment' => 'string|max:64',
        ];
    }

    public function authorize(): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::CUSTOMER_EDIT);
    }
}
