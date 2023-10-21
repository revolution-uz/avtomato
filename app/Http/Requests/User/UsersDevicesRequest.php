<?php

namespace App\Http\Requests\User;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;

class UsersDevicesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return match (Route::currentRouteName()) {
            'profile.devices' => RbacHelper::checkPermission(PermissionEnum::PROFILE_DEVICES),
            'users.devices' => RbacHelper::checkPermission(PermissionEnum::USER_DEVICES),
            default => false
        };
    }
}
