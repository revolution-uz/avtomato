<?php

namespace App\Http\Requests\Info;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use Illuminate\Foundation\Http\FormRequest;

class InfoSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return !auth()->user() || RbacHelper::checkPermission(PermissionEnum::INFO_SETTINGS);
    }
}
