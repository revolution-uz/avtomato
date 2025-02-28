<?php

namespace Database\Seeders\Company;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Enums\Company\Rbac\UserRoleEnum;
use App\Models\Company\Branch;
use App\Models\Company\Rbac\Permission;
use App\Models\Company\Rbac\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Throwable;

class AdminRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /** @var Role $adminRole */
        $adminRole = Role::query()->firstOrCreate(['name' => UserRoleEnum::ADMIN->value]);

        try {
            $permissions = Permission::all();
            foreach ($permissions as $permission) {
                if (
                    ! $adminRole->permissions()->where('permissions.id', $permission->id)->exists()
                ) {
                    if (PermissionEnum::getIsBranchRelated($permission->slug)) {
                        foreach (Branch::all() as $branch) {
                            if (
                                ! $adminRole->permissions()
                                    ->where('permissions.id', $permission->id)
                                    ->where('branch_id', $branch->id)
                                    ->exists()
                            ) {
                                $adminRole->permissions()->attach($permission->id, ['branch_id' => $branch->id]);
                            }
                        }
                    } else {
                        $adminRole->permissions()->attach($permission->id);
                    }
                }
            }
        } catch (Throwable $e) {
            Log::error($e->getMessage());
        }
    }
}
