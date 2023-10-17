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
            'id' => 1
        ], [
            'id' => 1,
            'name' => 'MyUzkard',
            'name_uz' => 'My Uzkard',
            'name_ru' => 'Мой Узкарт',
            'name_en' => 'My Uzcard',
            'is_system' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        TransactionType::query()->firstOrCreate([
            'id' => 2
        ], [
            'id' => 2,
            'name' => 'Naqd',
            'name_uz' => 'Naqd',
            'name_ru' => 'Наличные',
            'name_en' => 'Cash',
            'is_system' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
