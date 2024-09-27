<?php

namespace Tests\Unit\Commands;

use App\Console\Commands\PopulateDebtSmsJobs;
use App\Enums\Company\InstallmentStatusEnum;
use App\Jobs\SendDebtSmsJob;
use App\Models\Company\Installment;
use App\Models\Company\Transaction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Bus;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PopulateDebtSmsJobsTest extends TestCase
{
    protected bool $tenancy = true;

    #[Test]
    public function doesPopulateCorrectDebtorInstallments(): void
    {
        Bus::fake();

        /** @var Installment $installment */
        $installment = Installment::factory()->create(['status' => InstallmentStatusEnum::ACTIVE, 'starts_in' => Carbon::now()]);

        (new PopulateDebtSmsJobs)->handle();

        Bus::assertDispatched(
            SendDebtSmsJob::class,
            fn ($job) => $job->installmentId === $installment->id and $job->queue === 'debt-sms' and $job->delay === 0
        );
    }

    #[Test]
    public function doNotPopulate(): void
    {
        Bus::fake();

        // Payment day not today
        Installment::factory()->create(['starts_in' => Carbon::now()->subDay()]);
        // Inactive
        Installment::factory()->create(['status' => InstallmentStatusEnum::INACTIVE]);
        // Not debtor
        /** @var Installment $installment */
        $installment = Installment::factory()->create(['starts_in' => Carbon::now()]);
        Transaction::factory()->create(['installment_id' => $installment->id, 'amount' => $installment->debtAmount]);

        (new PopulateDebtSmsJobs)->handle();

        Bus::assertNotDispatched(SendDebtSmsJob::class);
    }
}
