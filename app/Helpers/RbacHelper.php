<?php

namespace App\Helpers;

use App\Enums\Company\CompanyStatusEnum;
use App\Enums\Company\Rbac\PermissionEnum;
use App\Models\Company\Rbac\Permission;
use App\Models\Company\Rbac\RolePermission;
use GraphQL\Error\Error;

class RbacHelper
{
    public static function checkPermission(PermissionEnum $permission): bool
    {
        if ($permission->isRelatedToCompanyStatus() and tenant('status') != CompanyStatusEnum::ACTIVE) {
            throw new Error(__('api.inactive_company'));
        }

        return Permission::query()
            ->where('slug', $permission)
            ->whereHas('feature', fn ($q) => $q->where('is_active', true))
            ->whereHas('roles', fn ($q) => $q->where('roles.id', auth('company-admin')->user()->role_id))
            ->exists();
    }

    public static function getEnabledBranches(PermissionEnum $permission): array
    {
        $branches = RolePermission::query()
            ->join('permissions', 'role_permissions.permission_id', '=', 'permissions.id')
            ->join('features', 'permissions.feature_id', '=', 'features.id')
            ->where('slug', $permission)
            ->where('is_active', true)
            ->where('role_id', auth()->user()->role_id)
            ->pluck('branch_id')
            ->toArray();

        $filtered = array_filter($branches, function ($branch) {
            return $branch !== null;
        });

        return array_values($filtered);
    }
}
