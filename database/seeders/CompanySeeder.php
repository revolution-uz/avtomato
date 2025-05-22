<?php

namespace Database\Seeders;

use Database\Seeders\Company\AdminRoleSeeder;
use Database\Seeders\Company\AdminSeeder;
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
        $this->call(AdminSeeder::class);

        activity()->enableLogging();
    }
}
