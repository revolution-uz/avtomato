<?php

namespace Database\Seeders\Company;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Models\Company\Rbac\Permission;
use App\Models\Company\Rbac\PermissionGroup;
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
                foreach (PermissionEnum::list() as $group => $permissions) {
                    $permissionGroup = PermissionGroup::firstOrCreate([
                        'slug' => $group,
                    ]);

                    foreach ($permissions as $permission) {
                        Permission::firstOrCreate([
                            'slug' => $permission,
                            'permission_group_id' => $permissionGroup->id,
                            'is_branch_related' => PermissionEnum::getIsBranchRelated($permission),
                            'feature_id' => PermissionEnum::getFeatureId($permission),
                        ]);

                    }
                }
            });
        } catch (Throwable $e) {
            Log::error($e->getMessage());
        }
    }
}
