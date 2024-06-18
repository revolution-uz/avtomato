<?php

namespace App\Http\Requests\Customer;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use App\Models\Company\Card;
use App\Models\Company\InstallmentCard;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'CardsDeleteRequest',
    title: 'Request to delete cards',
    properties: [
        new OA\Property(property: 'ids', type: 'array', items: new OA\Items(type: 'int'), example: '[1, 2, 3]'),
    ],
)]
/**
 * @property int[] $ids
 */
class CardsDeleteRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'ids' => 'array',
            'ids.*' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    if (! Card::query()->where('id', $value)->exists()) {
                        $fail(__('api.not found'));
                    }
                    if (InstallmentCard::query()->where('card_id', $value)->exists()) {
                        $fail(__('api.are used', [
                            'item' => __('api.Card'),
                            'element' => __('api.Installment'),
                        ]));
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
