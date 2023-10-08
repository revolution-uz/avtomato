<?php

namespace App\Models\Company\Rbac;

use App\DTOs\PermissionRequestDTO;
use App\Enums\Company\Rbac\UserRoleEnum;
use App\Models\Company\Branch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
    ];

    public static function getAdmin()
    {
        return self::query()->whereName(UserRoleEnum::ADMIN)->first();
    }

    public function syncPermissions(...$permissions): void
    {
        $this->permissions()->detach();
        $collection = PermissionRequestDTO::collection($permissions[0]);
        foreach ($collection as $permissionDto) {
            $this->permissions()->attach($permissionDto->permission_id, ['branch_id' => $permissionDto->branch_id]);
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
        $rolePermissions = $this->rolePermissions;
        $branches = Branch::all();
        $permissionBranches = $this->rolePermissions()->get()->mapToGroups(function ($item, $key) {
            return [$item['permission_id'] => $item['branch_id']];
        })->toArray();

        $result = [];
        foreach ($rolePermissions as $rolePermission) {
            $result[$rolePermission->permission->slug] = [];
            foreach ($branches as $branch) {
                $result[$rolePermission->permission->slug]['branch_' . $branch->id] = in_array($branch->id, $permissionBranches[$rolePermission->permission_id] ?? [], true);
            }
            // Add All branch
            $result[$rolePermission->permission->slug]['all'] = $rolePermission->branch_id === null;
        }

        return $result;
    }

    public function mapAllPermissionByBranch()
    {
        $permissionBranches = $this->rolePermissions()->get()->mapToGroups(function ($item, $key) {
            return [$item['permission_id'] => $item['branch_id']];
        })->toArray();

        $result = [];
        $groups = PermissionGroup::all();
        foreach ($groups as $group) {
            $groupItem['id'] = $group->id;
            $groupItem['slug'] = $group->slug;
            $groupItem['name'] = $group->name;
            $groupItem['permissions'] = [];
            foreach ($group->permissions as $permission) {
                $permissionItem['id'] = $permission->id;
                $permissionItem['slug'] = $permission->slug;
                $permissionItem['name'] = $permission->name;
                $permissionItem['branches'] = [];
                foreach (Branch::all() as $branch) {
                    $branchItem['id'] = $branch->id;
                    $branchItem['name'] = $branch->name;
                    $branchItem['editable'] = $permission->is_branch_related;
                    $branchItem['enabled'] = in_array($branch->id, $permissionBranches[$permission->id] ?? [], true);

                    $permissionItem['branches'][] = $branchItem;
                }
                // Add All branch
                $permissionItem['branches'][] = [
                    'id' => null,
                    'name' => 'All',
                    'editable' => true,
                    'enabled' => in_array(null, $permissionBranches[$permission->id] ?? [], true),
                ];
                $groupItem['permissions'][] = $permissionItem;
            }

            $result[] = $groupItem;
        }

        return $result;
    }
}
