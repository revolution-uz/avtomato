<?php

namespace Tests\Unit\Commands;

use App\Console\Commands\PopulateInstallmentChargeViaAtmosCardJobs;
use App\Enums\Company\InstallmentStatusEnum;
use App\Enums\FeatureEnum;
use App\Jobs\InstallmentChargeViaAtmosCardJob;
use App\Models\Company;
use App\Models\Company\Feature;
use App\Models\Company\Installment;
use App\Models\Company\Transaction;
use App\Services\User\BalanceService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Bus;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PopulateInstallmentChargeViaAtmosCardJobsTest extends TestCase
{
    protected bool $tenancy = true;
    protected bool $multiTenancies = true;

    protected function setUp(): void
    {
        parent::setUp();

        Bus::fake();
    }

    #[Test]
    public function isGetsCorrectInstallments(): void
    {
        $this->mockBalanceService();

        $installment = Installment::factory()->create(['status' => InstallmentStatusEnum::ACTIVE, 'starts_in' => now()]);

        (new PopulateInstallmentChargeViaAtmosCardJobs)->handle();

        $dispatchedJobs = Bus::dispatched(InstallmentChargeViaAtmosCardJob::class);
        $this->assertCount(1, $dispatchedJobs);
        $this->assertEquals('installment-charge-via-atmos-card-' . tenant('id') . '-' . $installment->id, $dispatchedJobs[0]->uniqueId());
        $this->assertEquals('installment-charge-via-atmos-card', $dispatchedJobs[0]->queue);
        $this->assertTrue($dispatchedJobs[0]->delay instanceof Carbon);
        $this->assertGreaterThanOrEqual(0, $dispatchedJobs[0]->delay->diffInSeconds());
    }

    #[Test]
    public function dontPopulateIfStartDateIsSoonAndInitialFeeNotPaid(): void
    {
        $this->mockBalanceService();

        Installment::factory()->create(['status' => InstallmentStatusEnum::ACTIVE, 'starts_in' => now()->addDay()]);

        (new PopulateInstallmentChargeViaAtmosCardJobs)->handle();

        Bus::assertNotDispatched(InstallmentChargeViaAtmosCardJob::class);
    }

    #[Test]
    public function dontPopulateIfStartsSoon(): void
    {
        $this->mockBalanceService();

        $installment = Installment::factory()->create(['status' => InstallmentStatusEnum::ACTIVE, 'starts_in' => now()->addDay()]);
        Transaction::factory()->create(['installment_id' => $installment->id, 'amount' => $installment->initial_fee, 'is_initial_fee' => true]);

        (new PopulateInstallmentChargeViaAtmosCardJobs)->handle();

        Bus::assertNotDispatched(InstallmentChargeViaAtmosCardJob::class);
    }

    #[Test]
    public function dontPopulateIfPaid(): void
    {
        $this->mockBalanceService();

        $installment = Installment::factory()->create(['status' => InstallmentStatusEnum::ACTIVE, 'starts_in' => now()->subDay()]);
        Transaction::factory()->create(['installment_id' => $installment->id, 'amount' => $installment->initial_fee, 'is_initial_fee' => true]);
        Transaction::factory()->create(['installment_id' => $installment->id, 'amount' => $installment->monthlyPayment]);

        (new PopulateInstallmentChargeViaAtmosCardJobs)->handle();

        Bus::assertNotDispatched(InstallmentChargeViaAtmosCardJob::class);
    }

    #[Test]
    public function successCheckingForTwoAvailableCompanies(): void
    {
        $this->mockBalanceService();

        foreach (Company::query()->get() as $company) {
            tenancy()->initialize($company);
            Installment::factory()->create(['status' => InstallmentStatusEnum::ACTIVE, 'starts_in' => Carbon::now()]);
        }

        (new PopulateInstallmentChargeViaAtmosCardJobs)->handle();

        $dispatchedJobs = Bus::dispatched(InstallmentChargeViaAtmosCardJob::class);
        $this->assertCount(2, $dispatchedJobs);
        /** @var InstallmentChargeViaAtmosCardJob $job */
        foreach ($dispatchedJobs as $job) {
            $this->assertEquals('installment-charge-via-atmos-card-' . tenant('id') . '-' . 1, $job->uniqueId());
            $this->assertEquals('installment-charge-via-atmos-card', $job->queue);
            $this->assertTrue($dispatchedJobs[0]->delay instanceof Carbon);
            $this->assertGreaterThanOrEqual(0, $dispatchedJobs[0]->delay->diffInSeconds());
        }
    }

    #[Test]
    public function successWithOneAnyActiveFeature(): void
    {
        $this->mockBalanceService();

        foreach (Company::all() as $company) {
            tenancy()->initialize($company);
            Feature::findByName(FeatureEnum::INSTALLMENT_AUTO_CHARGE)->update(['is_active' => true]);
            Feature::findByName(FeatureEnum::INSTALLMENT_AUTO_CHARGE_BY_PINFL)->update(['is_active' => false]);
            Installment::factory()->count(5)->create(['status' => InstallmentStatusEnum::ACTIVE, 'starts_in' => now()]);
        }

        (new PopulateInstallmentChargeViaAtmosCardJobs)->handle();

        Bus::assertDispatchedTimes(InstallmentChargeViaAtmosCardJob::class, 10);
    }

    #[Test]
    public function failWithInactiveFeature(): void
    {
        $this->mockBalanceService(0);

        foreach (Company::all() as $company) {
            tenancy()->initialize($company);
            Feature::findByName(FeatureEnum::INSTALLMENT_AUTO_CHARGE)->update(['is_active' => false]);
            Feature::findByName(FeatureEnum::INSTALLMENT_AUTO_CHARGE_BY_PINFL)->update(['is_active' => false]);
            Installment::factory()->count(5)->create(['status' => InstallmentStatusEnum::ACTIVE, 'starts_in' => now()]);
        }

        (new PopulateInstallmentChargeViaAtmosCardJobs)->handle();

        Bus::assertNotDispatched(InstallmentChargeViaAtmosCardJob::class);
    }

    #[Test]
    public function failWithInsufficientBalance(): void
    {
        $this->mockBalanceService(2, false);

        foreach (Company::all() as $company) {
            tenancy()->initialize($company);
            Installment::factory()->count(5)->create(['status' => InstallmentStatusEnum::ACTIVE, 'starts_in' => now()]);
        }

        (new PopulateInstallmentChargeViaAtmosCardJobs)->handle();

        Bus::assertNotDispatched(InstallmentChargeViaAtmosCardJob::class);
    }

    private function mockBalanceService(int $callCount = 2, bool $returnValue = true): void
    {
        $this->instance(
            BalanceService::class,
            Mockery::mock(BalanceService::class, function (MockInterface $mock) use ($callCount, $returnValue) {
                $mock->shouldReceive('isAvailableCompany')
                    ->times($callCount)
                    ->andReturn($returnValue);
            })->makePartial()
        );
    }
}
