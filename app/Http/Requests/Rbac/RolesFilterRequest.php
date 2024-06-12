<?php

namespace App\Http\Requests\Rbac;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;
use App\Http\Requests\BaseFilterRequest;

class RolesFilterRequest extends BaseFilterRequest
{
    public function authorize(): bool
    {
        return true;

        return RbacHelper::checkPermission(PermissionEnum::ROLE_VIEW_ALL);
    }
}
