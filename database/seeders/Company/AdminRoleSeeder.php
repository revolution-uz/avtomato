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
        /** @var Role $admin */
        $admin = Role::query()->firstOrCreate(['name' => UserRoleEnum::ADMIN->value]);

        try {
            $permissions = Permission::all();
            foreach ($permissions as $permission) {
                if (
                    ! $admin->permissions()->where('permission_id', $permission->id)->exists()
                ) {
                    $admin->permissions()->attach($permission->id);
                }

                if (PermissionEnum::getIsBranchRelated($permission->slug)) {
                    foreach (Branch::all() as $branch) {
                        if (
                            ! $admin->permissions()
                                ->where('permission_id', $permission->id)
                                ->where('branch_id', $branch->id)
                                ->exists()
                        ) {
                            $admin->permissions()->attach($permission->id, ['branch_id' => $branch->id]);
                        }
                    }
                }
            }

        } catch (Throwable $e) {
            $this->command->error('Error: ' . $e->getMessage());
            Log::error($e->getMessage());
        }
    }
}
