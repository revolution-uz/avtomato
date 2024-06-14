<?php

namespace App\Http\Requests\Management;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use App\Models\Company\Branch;
use App\Models\Company\Installment;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'BranchesDeleteRequest',
    title: 'Request to delete branches',
    properties: [
        new OA\Property(property: 'ids', type: 'array', items: new OA\Items(type: 'int'), example: '[1, 2, 3]'),
    ],
)]
/**
 * @property int[] $ids
 */
class BranchesDeleteRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'ids' => 'array',
            'ids.*' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    if (! Branch::query()->where('id', $value)->exists()) {
                        $fail(__('api.not found'));
                    }
                    if (Installment::query()->where('branch_id', $value)->exists()) {
                        $fail(__('api.are used', [
                            'item' => __('api.Branch'),
                            'element' => __('api.Installment'),
                        ]));
                    }
                },
            ],
        ];
    }

    public function authorize()
    {
        return RbacHelper::checkPermission(PermissionEnum::BRANCH_DELETE);
    }
}
