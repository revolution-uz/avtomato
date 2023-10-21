<?php

namespace App\Http\Requests\Rbac;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

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
            RbacHelper::checkPermission(PermissionEnum::CUSTOMER_CARDS_EDIT) :
            RbacHelper::checkPermission(PermissionEnum::CUSTOMER_CARDS_CREATE);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:roles,name' . ($this->id ? ',' . $this->id : ''),
        ];
    }
}
