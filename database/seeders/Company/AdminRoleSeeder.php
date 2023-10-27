<?php

namespace Database\Seeders\Company;

use App\Enums\Company\Rbac\UserRoleEnum;
use App\Models\Company\Rbac\Permission;
use App\Models\Company\Rbac\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
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
            DB::transaction(static function () use ($admin) {
                $permissions = Permission::all();
                foreach ($permissions as $permission) {
                    $admin->permissions()->attach($permission->id);
                }
            });
        } catch (Throwable $e) {
            Log::error($e->getMessage());
        }
    }
}
