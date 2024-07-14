<?php

namespace App\Models\Company\Rbac;

use App\Enums\Company\Rbac\UserRoleEnum;
use App\Models\Company\Branch;
use App\Models\Company\CompanyAdmin;
use App\Models\Company\OrderBySortable;
use App\Traits\LogsActivityDirtyOnlyTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

/**
 * App\Models\Company\Rbac\Role
 *
 * @property int $id
 * @property string $name
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon|null $deleted_at
 * @property-read RolePermission[] $rolePermissions
 * @property-read Permission[] $permissions
 * @property CompanyAdmin[]|Collection<int,CompanyAdmin> $admins
 */
class Role extends Model
{
    use HasFactory, LogsActivityDirtyOnlyTrait, OrderBySortable, SoftDeletes;

    public static array $sortables = ['id', 'name', 'created_at', 'updated_at'];

    protected $fillable = [
        'name',
    ];

    public static function getAdmin(): Role
    {
        /** @var self $adminRole */
        $adminRole = self::query()->where('name', UserRoleEnum::ADMIN)->first();

        return $adminRole;
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

    public function admins(): HasMany
    {
        return $this->hasMany(CompanyAdmin::class, 'role_id', 'id');
    }

    public function mapRolePermissionByBranch(): array
    {
        $rolePermissions = $this->rolePermissions;
        $result = [];

        foreach ($rolePermissions as $rolePermission) {
            if ($rolePermission->permission->feature?->is_active === false) {
                continue;
            }

            $result[$rolePermission->permission->slug->value] = $result[$rolePermission->permission->slug->value] ?? [];

            if ($rolePermission->branch_id !== null) {
                $result[$rolePermission->permission->slug->value]['branch_' . $rolePermission->branch_id] = true;
            } else {
                $result[$rolePermission->permission->slug->value]['all'] = true;
            }
        }

        return $result;
    }

    public function mapAllPermissionByBranch(): array
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

                $allBranchEnabled = true;
                foreach (Branch::all() as $branch) {
                    $branchItem['id'] = $branch->id;
                    $branchItem['name'] = $branch->name;
                    $branchItem['editable'] = $permission->is_branch_related;
                    $branchItem['enabled'] = in_array($branch->id, $permissionBranches[$permission->id] ?? [], true);

                    if ($allBranchEnabled) {
                        $allBranchEnabled = $branchItem['enabled'];
                    }
                    $permissionItem['branches'][] = $branchItem;
                }
                // Add All branch
                $permissionItem['branches'][] = [
                    'id' => null,
                    'name' => 'All',
                    'editable' => true,
                    'enabled' => $allBranchEnabled,
                ];
                $groupItem['permissions'][] = $permissionItem;
            }

            $result[] = $groupItem;
        }

        return $result;
    }
}
