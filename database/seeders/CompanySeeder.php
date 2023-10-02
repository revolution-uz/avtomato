<?php

namespace Database\Seeders;

use App\Enums\Company\Rbac\UserRoleEnum;
use App\Enums\Company\UserStatusEnum;
use App\Models\Company\User;
use Database\Seeders\Company\FeatureSeeder;
use Database\Seeders\Company\LocationSeeder;
use Database\Seeders\Company\PermissionSeeder;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(LocationSeeder::class);
        $this->call(FeatureSeeder::class);
        $this->call(PermissionSeeder::class);
        // TODO remove before production
        /** @var User $user */
        $user = User::query()->firstOrCreate(
            ['username' => 'admin'],
            [
                'username' => 'admin',
                'first_name' => 'Yura',
                'last_name' => 'Sultonov',
                'status' => UserStatusEnum::ACTIVE,
                'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            ]
        );

        if (! $user->hasRole(UserRoleEnum::OWNER->value)) {
            $user->assignRole(UserRoleEnum::OWNER->value);
        }
    }
}
