<?php

namespace App\Http\Requests\User;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;

class RevokeDeviceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return match (Route::currentRouteName()) {
            'profile.revoke-device' => RbacHelper::checkPermission(PermissionEnum::PROFILE_REVOKE_DEVICE),
            'users.revoke-device' => RbacHelper::checkPermission(PermissionEnum::USER_DEVICES),
            default => false
        };
    }
}
