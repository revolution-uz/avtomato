<?php

namespace Database\Seeders\Company;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Enums\Company\Rbac\UserRoleEnum;
use App\Models\Company\Rbac\Permission;
use App\Models\Company\Rbac\PermissionGroup;
use App\Models\Company\Rbac\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Throwable;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::firstOrCreate(['name' => UserRoleEnum::OWNER->value]);

        try {
            DB::transaction(function () {
                foreach (PermissionEnum::list() as $group => $permissions) {
                    $permissionGroup = PermissionGroup::firstOrCreate([
                        'slug' => $group,
                    ]);

                    foreach ($permissions as $permission) {
                        Permission::firstOrCreate([
                            'slug' => $permission,
                            'permission_group_id' => $permissionGroup->id,
                            'is_branch_related' => PermissionEnum::getIsBranchRelated($permission),
                        ]);
                    }
                }
            });
        } catch (Throwable $e) {

        }
    }
}
