<?php

namespace Database\Seeders\Company;

use App\Enums\Company\UserRole;
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
        Role::findOrCreate(UserRole::OWNER->value, 'web');
        Role::findOrCreate(UserRole::OWNER->value, 'web');
    }
}
