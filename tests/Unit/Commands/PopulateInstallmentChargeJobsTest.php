<?php

namespace Tests\Unit\Commands;

use App\Console\Commands\PopulateInstallmentChargeJobs;
use App\Enums\Company\InstallmentStatusEnum;
use App\Jobs\InstallmentChargeJob;
use App\Models\Company\Installment;
use App\Models\Company\Transaction;
use Illuminate\Support\Facades\Bus;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PopulateInstallmentChargeJobsTest extends TestCase
{
    protected bool $tenancy = true;

    #[Test]
    public function isGetsCorrectInstallments(): void
    {
        Bus::fake();
        Installment::factory()->create([
            'status' => InstallmentStatusEnum::ACTIVE,
            'starts_in' => now(),
        ]);
        (new PopulateInstallmentChargeJobs)->handle();
        Bus::assertDispatched(InstallmentChargeJob::class);
    }

    #[Test]
    public function dontPopulateIfStartDateIsSoonAndInitialFeeNotPaid(): void
    {
        Bus::fake();
        Installment::factory()->create([
            'status' => InstallmentStatusEnum::ACTIVE,
            'starts_in' => now()->addDay(),
        ]);
        (new PopulateInstallmentChargeJobs)->handle();
        Bus::assertDispatched(InstallmentChargeJob::class);
    }

    #[Test]
    public function dontPopulateIfStartsSoon(): void
    {
        Bus::fake();
        $installment = Installment::factory()->create([
            'status' => InstallmentStatusEnum::ACTIVE,
            'starts_in' => now()->addDay(),
        ]);
        Transaction::factory()->create(['installment_id' => $installment->id, 'amount' => $installment->initial_fee]);
        (new PopulateInstallmentChargeJobs)->handle();
        Bus::assertNotDispatched(InstallmentChargeJob::class);
    }

    #[Test]
    public function dontPopulateIfPaid(): void
    {
        Bus::fake();
        $installment = Installment::factory()->create([
            'status' => InstallmentStatusEnum::ACTIVE,
            'starts_in' => now()->subDay(),
        ]);
        Transaction::factory()->create(['installment_id' => $installment->id, 'amount' => $installment->initial_fee]);
        Transaction::factory()->create(['installment_id' => $installment->id, 'amount' => $installment->monthlyPayment]);
        (new PopulateInstallmentChargeJobs)->handle();
        Bus::assertNotDispatched(InstallmentChargeJob::class);
    }
}
