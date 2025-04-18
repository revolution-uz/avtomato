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
            'id' => TransactionType::ATMOS_WITH_CARD,
        ], [
            'name_uz' => 'Atmos karta bilan',
            'name_ru' => 'Атмос с картой ',
            'name_en' => 'Atmos with card',
            'is_system' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        TransactionType::query()->firstOrCreate([
            'id' => TransactionType::ATMOS_WITH_PINFL,
        ], [
            'name_uz' => 'Atmos passport bilan',
            'name_ru' => 'Атмос с паспортом',
            'name_en' => 'Atmos with passport',
            'is_system' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        TransactionType::query()->updateOrCreate([
            'id' => TransactionType::UNIRED_WITH_PINFL,
        ], [
            'name_uz' => 'Unired passport bilan',
            'name_ru' => 'Униред с паспортом',
            'name_en' => 'Unired with passport',
            'is_system' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        TransactionType::query()->updateOrCreate([
            'id' => TransactionType::ICAN_INTEGRATION,
        ], [
            'name_uz' => 'ICAN integratsiyasi',
            'name_ru' => 'Интеграция ICAN',
            'name_en' => 'ICAN Integration',
            'is_system' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}
