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
        $owner = Role::create([
            'name' => \App\Enums\Company\Role::OWNER,
            'guard_name' => 'web'
        ]);

        $manager = Role::create([
            'name' => \App\Enums\Company\Role::SALES_MANAGER,
            'guard_name' => 'web'
        ]);
        User::first()->assignRole($owner);
    }
}
