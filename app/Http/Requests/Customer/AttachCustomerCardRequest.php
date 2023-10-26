<?php

namespace App\Http\Requests\Customer;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'AttachCustomerCardRequest',
    title: 'Request to create or update a card',
    properties: [
        new OA\Property(property: 'card_id', type: 'int', example: '1'),
        new OA\Property(property: 'phone', type: 'string', example: '+998901234567'),
    ],
)]

/**
 * @property int $card_id
 * @property string $phone
 */
class AttachCustomerCardRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'card_id' => 'required|exists:cards,id',
            'phone' => 'required|string',
        ];
    }

    public function authorize(): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::CUSTOMER_CARDS_ATTACH);
    }
}
