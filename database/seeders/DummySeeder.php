<?php

namespace Database\Seeders;

use App\Enums\Company\CompanyStatusEnum;
use App\Enums\Company\UserStatusEnum;
use App\Enums\Main\AdminRoleEnum;
use App\Models\Admin;
use App\Models\Company;
use App\Models\Company\User;
use Illuminate\Database\Seeder;

class DummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::query()->firstOrCreate(['email' => 'admin@admin.com'], [
            'email' => 'admin@admin.com',
            'password' => '$2y$10$9XqGfZiI6vmVhzJqGV/9w.CoA0BAp71o53n3x6J061nTVNdSPpjoa', // admin@701
            'role' => AdminRoleEnum::SUPER_ADMIN,
            'phone' => '+998933993300',
            'name' => 'Yura Sultonov',
        ]);
        /** @var Company $company */
        $company = Company::query()->firstOrCreate(['name' => 'Revolution Global'], [
            'name' => 'Revolution Global',
            'owner_phone' => '+998933993300',
            'owner_name' => 'Yura Sultonov',
            'status' => CompanyStatusEnum::CREATED,
        ]);

        $company->domains()->create(['domain' => 'revo.avtomato.test']);
        $company->domains()->create(['domain' => 'revo.avtomato.uz']);

        $company->run(function () {
            User::updateOrCreate(['username' => 'admin'], [
                'password' => '$2y$10$9XqGfZiI6vmVhzJqGV/9w.CoA0BAp71o53n3x6J061nTVNdSPpjoa', // admin@701
                'status' => UserStatusEnum::ACTIVE,
            ]);
        });
    }
}
