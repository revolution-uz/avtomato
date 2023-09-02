<?php

namespace Database\Seeders\Company;

use App\Models\Company\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create([
            'name' => \App\Enums\Company\UserRole::OWNER,
            'guard_name' => 'web'
        ]);
        Role::create([
            'name' => \App\Enums\Company\UserRole::SALES_MANAGER,
            'guard_name' => 'web'
        ]);
    }
}
