<?php

namespace Database\Seeders\Company;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Enums\Company\Rbac\PermissionGroupEnum;
use App\Models\Company\Rbac\Permission;
use App\Models\Company\Rbac\PermissionGroup;
use App\Models\Company\Rbac\RolePermission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        try {
            DB::transaction(static function () {
                foreach (PermissionGroupEnum::cases() as $group) {
                    /** @var PermissionGroup $permissionGroup */
                    $permissionGroup = PermissionGroup::query()->firstOrCreate([
                        'slug' => $group->value,
                    ]);

                    foreach (PermissionGroupEnum::getPermissionsByGroup($group) as $permission) {
                        Permission::query()->firstOrCreate(['slug' => $permission], [
                            'permission_group_id' => $permissionGroup->id,
                            'is_branch_related' => PermissionEnum::getIsBranchRelated($permission),
                            'feature_id' => PermissionEnum::getFeatureId($permission),
                        ]);
                    }
                    Permission::query()
                        ->whereNotIn('slug', PermissionGroupEnum::getPermissionsByGroup($group))
                        ->where('permission_group_id', $permissionGroup->id)
                        ->delete();
                }
                PermissionGroup::query()->whereNotIn('slug', PermissionGroupEnum::cases())->delete();
                Permission::query()->whereNotIn('permission_group_id', PermissionGroup::query()->pluck('id')->all())->delete();
                RolePermission::query()->whereNotIn('permission_id', Permission::query()->pluck('id')->all())->delete();
            });
        } catch (Throwable $e) {
            Log::error($e->getMessage());
        }
    }
}
