<?php

namespace App\Models\Company\Rbac;

use App\DTOs\PermissionRequestDTO;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property-read RolePermission[] $rolePermissions
 * @property-read Permission[] $permissions
 */
class Role extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
    ];

    protected $dateFormat = 'U';

    public function syncPermissions(...$permissions): void
    {
        $this->permissions()->detach();
        $collection = PermissionRequestDTO::collection($permissions[0]);
        foreach ($collection as $permissionDto) {
            $this->permissions()->attach($permissionDto->permission_ids, ['branch_id' => $permissionDto->branch_id]);
        }
    }

    public function rolePermissions(): HasMany
    {
        return $this->hasMany(RolePermission::class, 'role_id', 'id');
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permissions', 'role_id', 'permission_id')
            ->withPivot('branch_id');
    }

    public function mapRolePermissionByBranch(): array
    {
        $permissions = $this->rolePermissions;

        $result = [];
        foreach ($permissions as $permission) {
            $branchId = $permission->branch_id;
            $permissionId = $permission->permission_id;

            if (! isset($result[$branchId])) {
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
