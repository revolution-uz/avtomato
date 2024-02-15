<?php

namespace App\Http\Requests\User;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Enums\Company\UserStatusEnum;
use App\Helpers\RbacHelper;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'UserCreateRequest',
    title: 'User create body',
    properties: [
        new OA\Property(property: 'username', type: 'string', example: 'admin'),
        new OA\Property(property: 'first_name', type: 'string', example: 'Tom'),
        new OA\Property(property: 'last_name', type: 'string', example: 'Odell'),
        new OA\Property(property: 'status', type: 'int', enum: [1, 2], example: 1),
        new OA\Property(property: 'password', type: 'string', example: '12345678'),
        new OA\Property(property: 'password_confirmation', type: 'string', example: '12345678'),
    ],
)]
class UserCreateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'username' => 'required|string|max:50|unique:users,username',
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'status' => 'required|int|in:' . implode(',', UserStatusEnum::values()),
            'password' => 'required|string|min:8|max:50,confirmed',
            'avatar' => 'string|max:255',
        ];
    }

    public function authorize(): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::ADMIN_CREATE);
    }
}
