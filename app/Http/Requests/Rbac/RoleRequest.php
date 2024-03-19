<?php

namespace App\Http\Requests\Rbac;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use OpenApi\Attributes as OA;

/**
 * @property string $name
 */
#[OA\Schema(
    schema: 'RoleRequest',
    title: 'Request to create or update a role',
    properties: [
        new OA\Property(property: 'name', type: 'string', example: 'Role Name'),
    ],
)]
class RoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->id ?
            RbacHelper::checkPermission(PermissionEnum::ROLE_EDIT) :
            RbacHelper::checkPermission(PermissionEnum::ROLE_CREATE);
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
               Rule::unique('roles', 'name')
                   ->withoutTrashed()
                   ->ignore($this->id),
            ],
        ];
    }
}
