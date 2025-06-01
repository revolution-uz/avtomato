<?php

namespace Database\Seeders\Company;

use App\Models\Company\TransactionType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use Throwable;

class TransactionTypeSeeder extends Seeder
{
    public function run(): void
    {
        $transactionTypes = [
            TransactionType::ATMOS_WITH_CARD => ['name_uz' => 'Atmos karta bilan', 'name_ru' => 'Атмос с картой ', 'name_en' => 'Atmos with card'],
            TransactionType::ATMOS_WITH_PINFL => ['name_uz' => 'Atmos passport bilan', 'name_ru' => 'Атмос с паспортом', 'name_en' => 'Atmos with passport'],
            TransactionType::UNIRED_WITH_PINFL => ['name_uz' => 'Unired passport bilan', 'name_ru' => 'Униред с паспортом', 'name_en' => 'Unired with passport'],
        ];

        try {
            foreach ($transactionTypes as $id => $data) {
                TransactionType::query()->updateOrCreate(['id' => $id], array_merge($data, ['is_system' => true]));
            }

            TransactionType::query()
                ->where('is_system', true)
                ->whereNotIn('id', array_keys($transactionTypes))
                ->delete();

            Log::info('Transaction types seeded successfully.');

        } catch (Throwable $e) {
            $this->command->error('Error: ' . $e->getMessage());
            Log::error($e->getMessage());
        }
    }
}
