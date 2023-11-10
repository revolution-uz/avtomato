<?php

namespace App\Http\Requests;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;

/**
 * @property int $user_id
 */
class OwnLogsRequest extends BaseFilterRequest
{
    public function authorize(): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::ACTIVITY_LOG_VIEW_OWN);
    }
}
