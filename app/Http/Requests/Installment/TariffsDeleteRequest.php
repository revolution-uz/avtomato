<?php

namespace App\Http\Requests\Installment;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use App\Models\Company\Installment;
use App\Models\Company\Tariff;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'TariffsDeleteRequest',
    title: 'Request to delete tariffs',
    properties: [
        new OA\Property(property: 'ids', type: 'array', items: new OA\Items(type: 'int'), example: '[1, 2, 3]'),
    ],
)]
/**
 * @property int[] $ids
 */
class TariffsDeleteRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'ids' => 'array',
            'ids.*' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    if (! Tariff::query()->where('id', $value)->exists()) {
                        $fail(__('api.not found'));
                    }
                    if (Installment::query()->where('tariff_id', $value)->exists()) {
                        $fail(__('api.are used', [
                            'item' => __('api.Tariff'),
                            'element' => __('api.Installment'),
                        ]));
                    }
                },
            ],
        ];
    }

    public function authorize(): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::TARIFF_DELETE);
    }
}
