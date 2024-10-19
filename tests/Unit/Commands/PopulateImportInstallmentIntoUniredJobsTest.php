<?php

namespace Tests\Unit\Commands;

use App\Console\Commands\PopulateImportInstallmentIntoUniredJobs;
use App\Enums\Company\InstallmentStatusEnum;
use App\Enums\FeatureEnum;
use App\Jobs\ImportInstallmentIntoUniredJob;
use App\Models\Company;
use App\Models\Company\Feature;
use App\Models\Company\Installment;
use App\Models\Company\Transaction;
use App\Services\User\BalanceService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Bus;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PopulateImportInstallmentIntoUniredJobsTest extends TestCase
{
    protected bool $tenancy = true;
    protected bool $multiTenancies = true;

    private $_balanceServiceMock;

    protected function setUp(): void
    {
        parent::setUp();

        Bus::fake();
        $this->_balanceServiceMock = $this->mock(BalanceService::class);
    }

    #[Test]
    public function isGetsCorrectInstallments(): void
    {
        $this->_balanceServiceMock->shouldReceive('isBalanceEnough')->twice()->andReturn(true);
        $installment = Installment::factory()->create(['status' => InstallmentStatusEnum::ACTIVE, 'starts_in' => now()]);

        (new PopulateImportInstallmentIntoUniredJobs)->handle($this->_balanceServiceMock);

        $dispatchedJobs = Bus::dispatched(ImportInstallmentIntoUniredJob::class);
        $this->assertCount(1, $dispatchedJobs);
        $this->assertEquals(ImportInstallmentIntoUniredJob::JOB_ID . '-' . tenant('id') . '-' . $installment->id, $dispatchedJobs[0]->uniqueId());
        $this->assertEquals(ImportInstallmentIntoUniredJob::JOB_ID, $dispatchedJobs[0]->queue);
        $this->assertEquals(0, $dispatchedJobs[0]->delay);
    }

    #[Test]
    public function dontPopulateIfStartDateIsSoonAndInitialFeeNotPaid(): void
    {
        $this->_balanceServiceMock->shouldReceive('isBalanceEnough')->twice()->andReturn(true);
        Installment::factory()->create(['status' => InstallmentStatusEnum::ACTIVE, 'starts_in' => now()->addDay()]);

        (new PopulateImportInstallmentIntoUniredJobs)->handle($this->_balanceServiceMock);

        Bus::assertNotDispatched(ImportInstallmentIntoUniredJob::class);
    }

    #[Test]
    public function dontPopulateIfStartsSoon(): void
    {
        $this->_balanceServiceMock->shouldReceive('isBalanceEnough')->twice()->andReturn(true);
        $installment = Installment::factory()->create(['status' => InstallmentStatusEnum::ACTIVE, 'starts_in' => now()->addDay()]);
        Transaction::factory()->create(['installment_id' => $installment->id, 'amount' => $installment->initial_fee, 'is_initial_fee' => true]);

        (new PopulateImportInstallmentIntoUniredJobs)->handle($this->_balanceServiceMock);

        Bus::assertNotDispatched(ImportInstallmentIntoUniredJob::class);
    }

    #[Test]
    public function dontPopulateIfPaid(): void
    {
        $this->_balanceServiceMock->shouldReceive('isBalanceEnough')->twice()->andReturn(true);
        $installment = Installment::factory()->create(['status' => InstallmentStatusEnum::ACTIVE, 'starts_in' => now()->subDay()]);
        Transaction::factory()->create(['installment_id' => $installment->id, 'amount' => $installment->initial_fee, 'is_initial_fee' => true]);
        Transaction::factory()->create(['installment_id' => $installment->id, 'amount' => $installment->monthlyPayment]);

        (new PopulateImportInstallmentIntoUniredJobs)->handle($this->_balanceServiceMock);

        Bus::assertNotDispatched(ImportInstallmentIntoUniredJob::class);
    }

    #[Test]
    public function successCheckingForTwoAvailableCompanies(): void
    {
        $this->_balanceServiceMock->shouldReceive('isBalanceEnough')->twice()->andReturn(true);
        foreach (Company::query()->get() as $company) {
            tenancy()->initialize($company);
            Installment::factory()->create(['status' => InstallmentStatusEnum::ACTIVE, 'starts_in' => Carbon::now()]);
        }

        (new PopulateImportInstallmentIntoUniredJobs)->handle($this->_balanceServiceMock);

        $dispatchedJobs = Bus::dispatched(ImportInstallmentIntoUniredJob::class);
        $this->assertCount(2, $dispatchedJobs);
        /** @var ImportInstallmentIntoUniredJob $job */
        foreach ($dispatchedJobs as $job) {
            $this->assertEquals(ImportInstallmentIntoUniredJob::JOB_ID . '-' . tenant('id') . '-' . 1, $job->uniqueId());
            $this->assertEquals(ImportInstallmentIntoUniredJob::JOB_ID, $job->queue);
            $this->assertEquals(0, $job->delay);
        }
    }

    #[Test]
    public function failWithInactiveFeature(): void
    {
        $this->_balanceServiceMock->shouldReceive('isBalanceEnough')->times(0);
        foreach (Company::all() as $company) {
            tenancy()->initialize($company);
            Feature::findByName(FeatureEnum::INSTALLMENT_AUTO_CHARGE_BY_PINFL)->update(['is_active' => false]);
            Installment::factory()->count(5)->create(['status' => InstallmentStatusEnum::ACTIVE, 'starts_in' => now()]);
        }

        (new PopulateImportInstallmentIntoUniredJobs)->handle($this->_balanceServiceMock);

        Bus::assertNotDispatched(ImportInstallmentIntoUniredJob::class);
    }

    #[Test]
    public function failWithInsufficientBalance(): void
    {
        $this->_balanceServiceMock->shouldReceive('isBalanceEnough')->twice()->andReturn(false);
        foreach (Company::all() as $company) {
            tenancy()->initialize($company);
            Installment::factory()->count(5)->create(['status' => InstallmentStatusEnum::ACTIVE, 'starts_in' => now()]);
        }

        (new PopulateImportInstallmentIntoUniredJobs)->handle($this->_balanceServiceMock);

        Bus::assertNotDispatched(ImportInstallmentIntoUniredJob::class);
    }
}
