<?php

namespace App\Http\Requests\Rbac;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use App\Models\Company\CompanyAdmin;
use App\Models\Company\Rbac\Role;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'RolesDeleteRequest',
    title: 'Request to delete roles',
    properties: [
        new OA\Property(property: 'ids', type: 'array', items: new OA\Items(type: 'int'), example: '[1, 2, 3]'),
    ],
)]
/**
 * @property int[] $ids
 */
class RolesDeleteRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'ids' => 'array',
            'ids.*' => [
                'required',
                'integer',
                function ($attribute, $value, $fail) {
                    if (! Role::query()->where('id', $value)->exists()) {
                        $fail(__('api.not found'));
                    }
                    if ($value == Role::getAdmin()->id) {
                        $fail(__('api.You cannot delete', ['item' => Role::query()->where('id', $value)->first()->name]));
                    }
                    if (CompanyAdmin::query()->where('role_id', $value)->exists()) {
                        $fail(__('api.are used', [
                            'item' => __('api.Role'),
                            'element' => __('api.User'),
                        ]));
                    }
                },
            ],
        ];
    }

    public function authorize(): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::ROLE_DELETE);
    }
}
