<?php

namespace Database\Seeders\Company;

use App\Models\Company;
use App\Models\Company\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branches = Branch::exists();
        $company = Company::query()->findOrFail(tenant('id'));
        if (! $branches) {
            Branch::query()->create([
                'name' => 'Asosiy filial',
                'address' => $company->address ?? 'address',
                'inn' => $company->tin ?? 'inn',
            ]);
        }
    }
}
