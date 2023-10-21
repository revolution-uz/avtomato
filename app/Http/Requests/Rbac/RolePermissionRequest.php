<?php

namespace App\Http\Requests\Rbac;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'RolePermissionRequest',
    title: 'Request to create or update a role',
    properties: [
        new OA\Property(property: 'permission_id', type: 'int', example: 1),
        new OA\Property(property: 'branch_id', type: 'int', example: 1),
        new OA\Property(property: 'enabled', type: 'boolean', example: true),
    ],
)]
class RolePermissionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            '*.permission_id' => 'required|exists:permissions,id',
            '*.branch_id' => 'nullable|exists:branches,id',
            '*.enabled' => 'required|boolean',
        ];
    }

    public function authorize(): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::ROLE_PERMISSIONS);
    }
}
