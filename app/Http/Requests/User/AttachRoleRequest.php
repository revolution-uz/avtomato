<?php

namespace App\Http\Requests\User;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'AttachRoleRequest',
    title: 'Attach Role Request',
    properties: [
        new OA\Property(property: 'role_id', type: 'int', example: 1),
    ],
)]
class AttachRoleRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'role_id' => 'required|int|exists:roles,id',
        ];
    }

    public function authorize(): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::USER_CHANGE_ROLE);
    }
}
