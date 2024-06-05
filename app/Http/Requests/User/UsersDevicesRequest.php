<?php

namespace App\Http\Requests\User;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use App\Http\Requests\BaseFilterRequest;
use Illuminate\Support\Facades\Route;

/**
 * Class UsersDevicesRequest
 *
 * / */
class UsersDevicesRequest extends BaseFilterRequest
{
    public function authorize(): bool
    {
        return match (Route::currentRouteName()) {
            'profile.devices' => RbacHelper::checkPermission(PermissionEnum::PROFILE_DEVICES),
            'users.devices' => RbacHelper::checkPermission(PermissionEnum::ADMIN_DEVICES),
            default => false
        };
    }
}
