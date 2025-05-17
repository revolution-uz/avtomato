<?php

namespace Database\Seeders\Company;

use App\Exceptions\GeneralException;
use App\Models\Company\Transaction;
use App\Models\Company\TransactionType;
use App\Services\IcanService;
use Illuminate\Database\Seeder;
use Throwable;

class ChangeIcanTransactionsTypeSeeder extends Seeder
{
    private static array $_cache = [];

    public function run(): void
    {
        try {
            $transactionType = $this->getIdByIcanFlag('auto_payment');

            Transaction::query()
                ->whereNotNull('ican_id')
                ->update([
                    'transaction_type_id' => $transactionType,
                ]);

        } catch (Throwable $e) {
            $this->command->error('Error: ' . $e->getMessage());
        }
    }

    public function getIdByIcanFlag(string $flag): int
    {
        $tenantId = tenant('id');
        if (! isset(self::$_cache[$tenantId]) || empty(self::$_cache[$tenantId])) {
            self::$_cache[$tenantId] = TransactionType::query()
                ->whereNotNull('ican_flag')
                ->pluck('id', 'ican_flag')
                ->toArray();
        }

        if (! array_key_exists($flag, self::$_cache[$tenantId])) {
            app(IcanService::class)->syncTransactionTypes();
            self::$_cache[$tenantId] = TransactionType::query()
                ->whereNotNull('ican_flag')
                ->pluck('id', 'ican_flag')
                ->toArray();

            if (! array_key_exists($flag, self::$_cache[$tenantId])) {
                throw new GeneralException('Transaction type not found with ican flag: ' . $flag);
            }
        }

        return self::$_cache[$tenantId][$flag];
    }
}
