<?php

namespace Tests\Unit\Commands;

use App\Console\Commands\PopulateInstallmentChargeJobs;
use App\Enums\Company\InstallmentStatusEnum;
use App\Jobs\InstallmentChargeJob;
use App\Models\Company\Installment;
use App\Models\Company\Transaction;
use Illuminate\Contracts\Cache\Lock;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PopulateInstallmentChargeJobsTest extends TestCase
{
    protected bool $tenancy = true;

    protected function setUp(): void
    {
        parent::setUp();

        Bus::fake();

        $lockMock = Mockery::mock(Lock::class);
        $lockMock->shouldReceive('get')->andReturn(true);

        $cacheMock = Mockery::mock(Repository::class);
        $cacheMock->shouldReceive('lock')->andReturn($lockMock);

        Cache::shouldReceive('driver')->with('redis')->andReturn($cacheMock);
    }

    #[Test]
    public function isGetsCorrectInstallments(): void
    {
        $installment = Installment::factory()->create(['status' => InstallmentStatusEnum::ACTIVE, 'starts_in' => now()]);

        (new PopulateInstallmentChargeJobs)->handle();

        Bus::assertDispatched(
            InstallmentChargeJob::class,
            fn ($job): bool => $job->uniqueId() === 'installment-charge-' . tenant('id') . '-' . $installment->id
        );
    }

    #[Test]
    public function dontPopulateIfStartDateIsSoonAndInitialFeeNotPaid(): void
    {
        Installment::factory()->create(['status' => InstallmentStatusEnum::ACTIVE, 'starts_in' => now()->addDay()]);

        (new PopulateInstallmentChargeJobs)->handle();

        Bus::assertNotDispatched(InstallmentChargeJob::class);
    }

    #[Test]
    public function dontPopulateIfStartsSoon(): void
    {
        $installment = Installment::factory()->create(['status' => InstallmentStatusEnum::ACTIVE, 'starts_in' => now()->addDay()]);
        Transaction::factory()->create(['installment_id' => $installment->id, 'amount' => $installment->initial_fee, 'is_initial_fee' => true]);

        (new PopulateInstallmentChargeJobs)->handle();

        Bus::assertNotDispatched(InstallmentChargeJob::class);
    }

    #[Test]
    public function dontPopulateIfPaid(): void
    {
        $installment = Installment::factory()->create(['status' => InstallmentStatusEnum::ACTIVE, 'starts_in' => now()->subDay()]);
        Transaction::factory()->create(['installment_id' => $installment->id, 'amount' => $installment->initial_fee, 'is_initial_fee' => true]);
        Transaction::factory()->create(['installment_id' => $installment->id, 'amount' => $installment->monthlyPayment]);

        (new PopulateInstallmentChargeJobs)->handle();

        Bus::assertNotDispatched(InstallmentChargeJob::class);
    }
}
