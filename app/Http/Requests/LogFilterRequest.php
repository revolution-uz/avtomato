<?php

namespace App\Http\Requests;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Helpers\RbacHelper;

/**
 * @property int $admin_id
 */
class LogFilterRequest extends BaseFilterRequest
{
    public function authorize(): bool
    {
        return RbacHelper::checkPermission(PermissionEnum::ACTIVITY_LOG_VIEW_ALL);
    }

    public function rules(): array
    {
        return parent::rules() + [
            'admin_id' => 'int|exists:admins,id',
        ];
    }
}
