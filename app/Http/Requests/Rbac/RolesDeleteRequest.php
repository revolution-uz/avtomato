<?php

namespace App\Http\Requests\Rbac;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use App\Models\Company\Rbac\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
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
                Rule::notIn([Role::getAdmin()->id]),
            ],
        ];
    }

    public function messages()
    {
        return parent::messages() + [
            'ids.*.not_in' => 'You cannot delete the owner role',
        ];
    }

    public function authorize(): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::ROLE_DELETE);
    }
}
