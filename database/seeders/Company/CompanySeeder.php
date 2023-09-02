<?php

namespace Database\Seeders\Company;

use App\Enums\Company\UserStatus;
use App\Models\Company\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class CompanySeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(PermissionSeeder::class);
        // TODO remove before production
        User::query()->create(
            [
                'username' => 'admin',
                'first_name' => 'Yura',
                'last_name' => 'Sultonov',
                'status' => UserStatus::ACTIVE,
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            ]
        );
        User::query()->first()
            ->assignRole(Role::query()->where('name', \App\Enums\Company\UserRole::OWNER)->first());
    }
}
