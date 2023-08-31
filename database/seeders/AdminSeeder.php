<?php

namespace Database\Seeders;

use App\Enums\Main\AdminRole;
use App\Enums\Main\AdminStatus;
use App\Models\Admin;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Admin::create([
            'email' => 'admin@admin.com',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'role' => AdminRole::SUPER_USER,
//            'status' => AdminStatus::ACTIVE,
            'phone' => '+998901112233',
            'name' => 'Admin'
        ]);
    }
}
