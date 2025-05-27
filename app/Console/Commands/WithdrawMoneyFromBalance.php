<?php

namespace App\Console\Commands;

use App\Enums\Company\CompanyStatusEnum;
use App\Models\Company;
use App\Models\Company\BalanceCharge;
use App\Models\Company\Module;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;
use Symfony\Component\Console\Command\Command as CommandAlias;

class WithdrawMoneyFromBalance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'money:withdraw-balance';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @throws TenantCouldNotBeIdentifiedById
     */
    public function handle(): int
    {
        /** @var Company $company */
        foreach (Company::query()->get() as $company) {
            if ($company->status !== CompanyStatusEnum::ACTIVE or Carbon::now()->day !== $company->payment_day) {
                continue;
            }
            tenancy()->initialize($company);
            $activeModules = Module::query()->where('is_active', true)->get();
            /** @var Module $module */
            foreach ($activeModules as $module) {
                /** @var Company\Feature $feature */
                $feature = $module->features()->first();
                BalanceCharge::query()->create([
                    'amount' => $module->original_amount,
                    'feature_id' => $feature->id,
                    'chargeable_id' => $module->id,
                    'chargeable_type' => Module::class,
                ]);
            }

        }

        return CommandAlias::SUCCESS;
    }
}
