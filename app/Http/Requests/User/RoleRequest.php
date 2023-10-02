<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'RoleRequest',
    title: 'Request to create or update a role',
    properties: [
        new OA\Property(property: 'name', type: 'string', example: 'Role Name'),
        new OA\Property(property: 'permissions', type: 'array', items: new OA\Items(ref: '#/components/schemas/PermissionRequest')),
    ],
)]
#[OA\Schema(
    schema: 'PermissionRequest',
    title: 'Permission request',
    properties: [
        new OA\Property(property: 'branch_id', type: 'id', example: 1),
        new OA\Property(property: 'permission_ids', type: 'array', items: new OA\Items(type: 'int'), example: [1, 2, 3]),
    ],
)]
class RoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:roles,name' . ($this->role ? ',' . $this->role->id : ''),
            'permissions' => 'array',
            'permissions.*.branch_id' => 'nullable|integer|exists:branches,id',
            'permissions.*.permission_ids' => 'array|exists:permissions,id',
        ];
    }
}
