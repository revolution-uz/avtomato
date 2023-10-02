<?php

namespace App\Models\Company\Rbac;

use App\DTOs\PermissionRequestDTO;
use App\Models\RolePermission;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends \Spatie\Permission\Models\Role
{
    use SoftDeletes;

    protected $dateFormat = 'U';

    public function syncPermissions(...$permissions)
    {
        $this->permissions()->detach();
        $collection = PermissionRequestDTO::collection($permissions[0]);
            foreach ($collection as $permissionDto) {
                $this->permissions()->attach($permissionDto->permission_ids, ['branch_id' => $permissionDto->branch_id]);
            }
    }

    public function rolePermissions()
    {
        return $this->hasMany(RolePermission::class, 'role_id', 'id');
    }

    public function mapRolePermissionByBranch()
    {
        $permissions = $this->rolePermissions;

        $result = [];
        foreach ($permissions as $permission) {
            $branchId = $permission->branch_id;
            $permissionId = $permission->permission_id;

            if (!isset($result[$branchId])) {
                $result[$branchId] = [
                    'branch_id' => $branchId,
                    'permission_ids' => [],
                ];
            }

            $result[$branchId]['permission_ids'][] = $permissionId;
        }

        return $result;
    }
}
