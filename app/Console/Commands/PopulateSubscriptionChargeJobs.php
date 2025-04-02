<?php

namespace App\Console\Commands;

use App\Enums\FeatureEnum;
use App\Jobs\LoyaltyProgram\SubscriptionChargeJob;
use App\Models\Company;
use App\Models\Company\Feature;
use App\Models\Company\LoyaltyProgram\Subscription;
use App\Services\User\BalanceService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Symfony\Component\Console\Command\Command as CommandAlias;

class PopulateSubscriptionChargeJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'populate:subscription-charge-jobs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate subscription charge jobs for all active subscriptions';

    /**
     * Execute the console command.
     */
    public function handle(BalanceService $balanceService): int
    {
        $i = 0;

        foreach (Company::get() as $company) {
            tenancy()->initialize($company);

            if (! Feature::isFeatureActive(FeatureEnum::LOYALTY_PROGRAM) or ! $balanceService->isAvailableCompany()) {
                continue;
            }

            $subscriptionIds = Subscription::query()
                ->where('is_active', true)
                ->whereDate('expired_at', '<=', Carbon::today())
                ->pluck('id');

            if (! count($subscriptionIds)) {
                continue;
            }

            foreach ($subscriptionIds as $subscriptionId) {
                SubscriptionChargeJob::dispatch($subscriptionId)->delay(now()->addSeconds($i));
                $i++;
            }
        }

        return CommandAlias::SUCCESS;
    }
}
