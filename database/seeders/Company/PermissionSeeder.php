<?php

namespace Database\Seeders\Company;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Enums\Company\Rbac\UserRoleEnum;
use App\Models\Company\Rbac\Permission;
use App\Models\Company\Rbac\PermissionGroup;
use App\Models\Company\Rbac\Role;
use DB;
use Illuminate\Database\Seeder;
use Throwable;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::findOrCreate(UserRoleEnum::OWNER->value, 'web');

        try {
            DB::transaction(function () {
                foreach (PermissionEnum::list() as $group => $permissions) {
                    $permissionGroup = PermissionGroup::firstOrCreate([
                        'name' => $group,
                    ]);

                    foreach ($permissions as $permission) {
                        Permission::firstOrCreate([
                            'name' => $permission,
                            'permission_group_id' => $permissionGroup->id,
                            'is_branch_related' => PermissionEnum::getIsBranchRelated($permission),
                            'guard_name' => 'web',
                        ]);
                    }
                }
            });
        } catch (Throwable $e) {
            dd($e);
        }
    }
}
