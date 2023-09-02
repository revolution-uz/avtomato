<?php

namespace Database\Seeders;

use App\Enums\Company\CompanyStatus;
use App\Enums\Main\AdminRole;
use App\Models\Admin;
use App\Models\Company;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class DummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::query()->firstOrCreate(['email' => 'admin@admin.com'], [
            'email' => 'admin@admin.com',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'role' => AdminRole::SUPER_ADMIN,
            'phone' => '+998933993300',
            'name' => 'Yura Sultonov'
        ]);
        /** @var Company $company */
        $company = Company::query()->firstOrCreate(['subdomain' => 'revo'], [
            'name' => 'Revolution Global',
            'owner_phone' => '+998933993300',
            'owner_name' => 'Yura Sultonov',
            'subdomain' => 'revo',
            'db_user' => 'revo',
            'db_password' => Str::random(),
            'status' => CompanyStatus::CREATED
        ]);
        Artisan::call('company:create', ['company' => $company->id]);
    }
}
