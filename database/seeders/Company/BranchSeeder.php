<?php

namespace Database\Seeders\Company;

use App\Models\Company\Branch;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Throwable;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            $company = tenant();

            if (Branch::query()->doesntExist()) {
                Branch::query()->create([
                    'name' => 'Asosiy filial',
                    'address' => $company->address ?? 'address',
                    'inn' => $company->tin ?? 'inn',
                ]);
            }

        } catch (Throwable $e) {
            $this->command->error('Error: ' . $e->getMessage());
            Log::error($e->getMessage());
        }
    }
}
