<?php

namespace App\Console\Commands;

use App\Enums\Company\CompanySettingsEnum;
use App\Enums\FeatureEnum;
use App\Jobs\SendDebtSmsJob;
use App\Models\Company;
use App\Models\Company\CompanySettings;
use App\Models\Company\Feature;
use App\Models\Company\Installment;
use App\Models\Company\SmsTemplate;
use App\Services\Installment\InstallmentService;
use App\Services\User\BalanceService;
use Illuminate\Console\Command;

class PopulateDebtSmsJobs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'populate:debt-sms-jobs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate debt sms jobs for all active installments';

    /**
     * Execute the console command.
     */
    public function handle(BalanceService $balanceService): void
    {
        foreach (Company::query()->get() as $company) {
            tenancy()->initialize($company);
            $overdueDays = SmsTemplate::debtTemplates()->get()->pluck('data.overdue_days');

            if (
                CompanySettings::getValue(CompanySettingsEnum::SMS_INSTALLMENT_PAYMENT) == 0
                or ! Feature::findByName(FeatureEnum::SMS)->is_active
                or ! $balanceService->isBalanceEnough()
                or $overdueDays->isEmpty()
            ) {
                continue;
            }

            /** @var Installment[] $installments */
            $installments = InstallmentService::getDebtorInstallments($overdueDays->implode(','));

            $threshold = 5 * 60.0 / (count($installments) ?: 1);
            foreach ($installments as $index => $installment) {
                SendDebtSmsJob::dispatch($installment->id)->delay(intval($threshold * $index))->onQueue('debt-sms');
            }
        }
    }
}
