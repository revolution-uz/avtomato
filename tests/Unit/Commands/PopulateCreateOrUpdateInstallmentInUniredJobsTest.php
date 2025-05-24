<?php

namespace Tests\Unit\Commands;

use App\Enums\Company\InstallmentStatusEnum;
use App\Enums\FeatureEnum;
use App\Jobs\CreateOrUpdateInstallmentInUniredJob;
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

class PopulateCreateOrUpdateInstallmentInUniredJobsTest extends TestCase
{
    protected bool $tenancy = true;
    protected bool $multiTenancies = true;

    private string $_commandName = 'populate:create-or-update-installment-in-unired-jobs';

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

        $this->artisan($this->_commandName);

        $dispatchedJobs = Bus::dispatched(CreateOrUpdateInstallmentInUniredJob::class);
        $this->assertCount(2, $dispatchedJobs);
        $this->assertEquals(CreateOrUpdateInstallmentInUniredJob::JOB_ID . '-' . tenant('id') . '-' . $installment->id, $dispatchedJobs[0]->uniqueId());
        $this->assertEquals(CreateOrUpdateInstallmentInUniredJob::JOB_ID, $dispatchedJobs[0]->queue);
        $this->assertEquals(0, $dispatchedJobs[0]->delay);
    }

    #[Test]
    public function dontPopulateIfStartDateIsSoonAndInitialFeeNotPaid(): void
    {
        $this->mockBalanceService();
        Installment::factory()->create(['status' => InstallmentStatusEnum::ACTIVE, 'starts_in' => now()->addDay()]);

        $this->artisan($this->_commandName);

        Bus::assertDispatched(CreateOrUpdateInstallmentInUniredJob::class);
    }

    #[Test]
    public function dontPopulateIfStartsSoon(): void
    {
        $this->mockBalanceService();
        $installment = Installment::factory()->create(['status' => InstallmentStatusEnum::ACTIVE, 'starts_in' => now()->addDay()]);
        Transaction::factory()->create(['installment_id' => $installment->id, 'amount' => $installment->initial_fee, 'is_initial_fee' => true]);

        $this->artisan($this->_commandName);

        Bus::assertDispatched(CreateOrUpdateInstallmentInUniredJob::class);
    }

    #[Test]
    public function dontPopulateIfPaid(): void
    {
        $this->mockBalanceService();
        $installment = Installment::factory()->create(['status' => InstallmentStatusEnum::ACTIVE, 'starts_in' => now()->subDay()]);
        Transaction::factory()->create(['installment_id' => $installment->id, 'amount' => $installment->initial_fee, 'is_initial_fee' => true]);
        Transaction::factory()->create(['installment_id' => $installment->id, 'amount' => $installment->monthlyPayment]);

        $this->artisan($this->_commandName);

        Bus::assertDispatched(CreateOrUpdateInstallmentInUniredJob::class);
    }

    #[Test]
    public function successCheckingForTwoAvailableCompanies(): void
    {
        $this->mockBalanceService();
        foreach (Company::query()->get() as $company) {
            tenancy()->initialize($company);
            Installment::factory()->create(['status' => InstallmentStatusEnum::ACTIVE, 'starts_in' => Carbon::now()]);
        }

        $this->artisan($this->_commandName);

        $dispatchedJobs = Bus::dispatched(CreateOrUpdateInstallmentInUniredJob::class);
        $this->assertCount(4, $dispatchedJobs);
        /** @var CreateOrUpdateInstallmentInUniredJob $job */
        foreach ($dispatchedJobs as $job) {
            $this->assertEquals(CreateOrUpdateInstallmentInUniredJob::JOB_ID . '-' . tenant('id') . '-' . 1, $job->uniqueId());
            $this->assertEquals(CreateOrUpdateInstallmentInUniredJob::JOB_ID, $job->queue);
            $this->assertEquals(0, $job->delay);
        }
    }

    #[Test]
    public function failWithInactiveFeature(): void
    {
        $this->mockBalanceService(0);
        foreach (Company::all() as $company) {
            tenancy()->initialize($company);
            Feature::findByName(FeatureEnum::INSTALLMENT_AUTO_CHARGE_BY_PINFL)->update(['is_active' => false]);
            Installment::factory()->count(5)->create(['status' => InstallmentStatusEnum::ACTIVE, 'starts_in' => now()]);
        }

        $this->artisan($this->_commandName);

        Bus::assertDispatched(CreateOrUpdateInstallmentInUniredJob::class);
    }

    #[Test]
    public function failWithInsufficientBalance(): void
    {
        $this->mockBalanceService();
        foreach (Company::all() as $company) {
            tenancy()->initialize($company);
            Installment::factory()->count(5)->create(['status' => InstallmentStatusEnum::ACTIVE, 'starts_in' => now()]);
        }

        $this->artisan($this->_commandName);

        Bus::assertDispatched(CreateOrUpdateInstallmentInUniredJob::class);
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
