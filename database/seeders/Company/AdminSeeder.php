<?php

namespace Database\Seeders\Company;

use App\Enums\Company\UserStatusEnum;
use App\Models\Company\CompanyAdmin;
use App\Models\Company\Rbac\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            // TODO remove before production
            CompanyAdmin::query()->firstOrCreate([
                'username' => 'admin',
            ], [
                'first_name' => 'Yura',
                'last_name' => 'Sultonov',
                'status' => UserStatusEnum::ACTIVE,
                'password' => Hash::make('password'),
                'role_id' => Role::getAdmin()->id,
            ]);

            $icanIntegrator = CompanyAdmin::query()
                ->withTrashed()
                ->firstOrCreate([
                    'username' => CompanyAdmin::ICAN_INTEGRATOR,
                ], [
                    'first_name' => 'Ican',
                    'last_name' => 'Integrator',
                    'status' => UserStatusEnum::ACTIVE,
                    'password' => Hash::make(Str::random(8)),
                    'role_id' => Role::getAdmin()->id,
                ]);
            $icanIntegrator->delete(); // set deleted_at null

        } catch (Throwable $e) {
            $this->command->error('Error: ' . $e->getMessage());
            Log::error($e->getMessage());
        }
    }
}
