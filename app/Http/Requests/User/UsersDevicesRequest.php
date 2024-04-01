<?php

namespace App\Http\Requests\User;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Route;

/**
 * Class UsersDevicesRequest
 *
 * @property int $page
 * @property int $per_page
 * / */
class UsersDevicesRequest extends FormRequest
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
