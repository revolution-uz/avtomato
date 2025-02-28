<?php

namespace Database\Seeders;

use App\Enums\Company\Rbac\UserRoleEnum;
use App\Enums\Company\UserStatusEnum;
use App\Models\Company\CompanyAdmin;
use App\Models\Company\Rbac\Role;
use Database\Seeders\Company\AdminRoleSeeder;
use Database\Seeders\Company\BranchSeeder;
use Database\Seeders\Company\CompanySettingsSeeder;
use Database\Seeders\Company\EcommerceOrderStatusSeeder;
use Database\Seeders\Company\FeatureSeeder;
use Database\Seeders\Company\LocationSeeder;
use Database\Seeders\Company\ModuleSeeder;
use Database\Seeders\Company\PermissionSeeder;
use Database\Seeders\Company\SmsTemplateSeeder;
use Database\Seeders\Company\TransactionTypeSeeder;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        activity()->disableLogging();

        $this->call(LocationSeeder::class);
        $this->call(ModuleSeeder::class);
        $this->call(FeatureSeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(AdminRoleSeeder::class);
        $this->call(TransactionTypeSeeder::class);
        $this->call(CompanySettingsSeeder::class);
        $this->call(SmsTemplateSeeder::class);
        $this->call(BranchSeeder::class);
        $this->call(EcommerceOrderStatusSeeder::class);

        // TODO remove before production
        CompanyAdmin::query()->firstOrCreate([
            'username' => 'admin',
        ], [
            'username' => 'admin',
            'first_name' => 'Yura',
            'last_name' => 'Sultonov',
            'status' => UserStatusEnum::ACTIVE,
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'role_id' => Role::where('name', UserRoleEnum::ADMIN)->first()->id,
        ]);

        activity()->enableLogging();
    }
}
