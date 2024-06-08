<?php

namespace App\Http\Requests\User;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use OpenApi\Attributes as OA;

#[OA\Schema(
    schema: 'PasswordRequest',
    title: 'Password update body',
    properties: [
        new OA\Property(property: 'old_password', type: 'string', example: 'oldpassword'),
        new OA\Property(property: 'password', type: 'string', example: 'password'),
        new OA\Property(property: 'password_confirmation', type: 'string', example: 'password'),
    ],
)]
class PasswordRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'old_password' => ['required', 'string', 'min:8'],
            'password' => 'required|string|min:8|confirmed',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (!$this->checkOldPassword()) {
                $validator->errors()->add('old_password', 'The old password is incorrect.');
            }
        });
    }

    public function authorize(): bool
    {
        return match (Route::currentRouteName()) {
            'profile.change-password' => RbacHelper::checkPermission(PermissionEnum::PROFILE_CHANGE_PASSWORD),
            'users.change-password' => RbacHelper::checkPermission(PermissionEnum::ADMIN_CHANGE_PASSWORD),
            default => false
        };
    }

    protected function checkOldPassword()
    {
        return Hash::check($this->input('old_password'), auth()->user()->password);
    }
}
