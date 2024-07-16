<?php

namespace Database\Seeders\Company;

use App\Models\Company\TransactionType;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TransactionTypeSeeder extends Seeder
{
    public function run(): void
    {
        TransactionType::query()->firstOrCreate([
            'id' => 1,
        ], [
            'id' => 1,
            'name_uz' => 'Atmos karta bilan',
            'name_ru' => 'Атмос с картой ',
            'name_en' => 'Atmos with card',
            'is_system' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        TransactionType::query()->firstOrCreate([
            'id' => 2,
        ], [
            'id' => 2,
            'name_uz' => 'Atmos passport bilan',
            'name_ru' => 'Атмос с паспортом',
            'name_en' => 'Atmos with passport',
            'is_system' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        TransactionType::query()->firstOrCreate([
            'id' => 3,
        ], [
            'id' => 3,
            'name_uz' => 'Naqd',
            'name_ru' => 'Наличные',
            'name_en' => 'Cash',
            'is_system' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
