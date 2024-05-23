<?php

namespace App\Http\Requests\Installment;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;

/**
 * @property int $card_id
 */
#[OA\Schema(
    schema: 'InstallmentCardAttachRequest',
    title: 'Request to create a card',
    properties: [
        new OA\Property(property: 'card_id', type: 'int', example: '1'),
    ],
)]
/**
 * @propertu integer $card_id
 */
class InstallmentCardAttachRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'card_id' => [
                'required',
                // todo: check if the card belongs to the customer
                Rule::exists('cards', 'id'),
            ],
        ];
    }

    public function authorize(): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::INSTALLMENT_ATTACH_CARD);
    }
}
