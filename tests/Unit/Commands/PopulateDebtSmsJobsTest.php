<?php

namespace Tests\Unit\Commands;

use App\Console\Commands\PopulateDebtSmsJobs;
use App\Enums\Company\CompanySettingsEnum;
use App\Enums\Company\InstallmentStatusEnum;
use App\Enums\Company\SmsTemplateStatusEnum;
use App\Enums\FeatureEnum;
use App\Jobs\SendDebtSmsJob;
use App\Models\Company;
use App\Models\Company\CompanySettings;
use App\Models\Company\Feature;
use App\Models\Company\Installment;
use App\Models\Company\SmsTemplate;
use App\Services\User\BalanceService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Bus;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PopulateDebtSmsJobsTest extends TestCase
{
    protected bool $tenancy = true;
    protected bool $multiTenancies = true;

    private $_balanceServiceMock;

    protected function setUp(): void
    {
        parent::setUp();

        foreach (Company::all() as $company) {
            tenancy()->initialize($company);
            CompanySettings::where('name', CompanySettingsEnum::SMS_INSTALLMENT_PAYMENT)->update(['value' => '1']);
            for ($i = -1; $i <= 1; $i++) {
                SmsTemplate::factory()->create([
                    'template' => 'Test',
                    'status' => SmsTemplateStatusEnum::ACTIVE,
                    'data' => [
                        'category' => 'debt-templates',
                        'overdue_days' => $i,
                        'is_active' => true,
                    ],
                ]);
            }
        }

        Bus::fake();
        $this->_balanceServiceMock = $this->mock(BalanceService::class);
    }

    #[Test]
    public function populateCorrectDebtorInstallmentsForToday(): void
    {
        $this->_balanceServiceMock->shouldReceive('isAvailableCompany')->twice()->andReturn(true);
        foreach (Company::query()->get() as $company) {
            tenancy()->initialize($company);
            Installment::factory()->create(['status' => InstallmentStatusEnum::ACTIVE, 'starts_in' => Carbon::today()]);
        }

        (new PopulateDebtSmsJobs)->handle($this->_balanceServiceMock);

        $dispatchedJobs = Bus::dispatched(SendDebtSmsJob::class);
        $this->assertCount(2, $dispatchedJobs);
        /** @var SendDebtSmsJob $job */
        foreach ($dispatchedJobs as $job) {
            $this->assertEquals(1, $job->installmentId);
            $this->assertEquals('sms', $job->queue);
            $this->assertEquals(0, $job->delay);
        }
    }

    #[Test]
    public function populateCorrectDebtorInstallmentsBeforeOneDay(): void
    {
        $this->_balanceServiceMock->shouldReceive('isAvailableCompany')->twice()->andReturn(true);
        /** @var Installment $installment */
        $installment = Installment::factory()->create(['status' => InstallmentStatusEnum::ACTIVE, 'starts_in' => Carbon::today()->subDay()]);

        (new PopulateDebtSmsJobs)->handle($this->_balanceServiceMock);

        $dispatchedJobs = Bus::dispatched(SendDebtSmsJob::class);
        $this->assertCount(1, $dispatchedJobs);
        $this->assertEquals($installment->id, $dispatchedJobs[0]->installmentId);
        $this->assertEquals('sms', $dispatchedJobs[0]->queue);
        $this->assertEquals(0, $dispatchedJobs[0]->delay);
    }

    #[Test]
    public function populateCorrectDebtorInstallmentsAfterOneDay(): void
    {
        $this->_balanceServiceMock->shouldReceive('isAvailableCompany')->twice()->andReturn(true);
        /** @var Installment $installment */
        $installment = Installment::factory()->create(['status' => InstallmentStatusEnum::ACTIVE, 'starts_in' => Carbon::today()->addDay()]);

        (new PopulateDebtSmsJobs)->handle($this->_balanceServiceMock);

        $dispatchedJobs = Bus::dispatched(SendDebtSmsJob::class);
        $this->assertCount(1, $dispatchedJobs);
        $this->assertEquals($installment->id, $dispatchedJobs[0]->installmentId);
        $this->assertEquals('sms', $dispatchedJobs[0]->queue);
        $this->assertEquals(0, $dispatchedJobs[0]->delay);
    }

    #[Test]
    public function successCheckingForOneAvailableCompany(): void
    {
        $this->_balanceServiceMock->shouldReceive('isAvailableCompany')->once()->andReturn(true);
        Feature::findByName(FeatureEnum::SMS)->update(['is_active' => false]);
        foreach (Company::query()->get() as $company) {
            tenancy()->initialize($company);
            Installment::factory()->create(['status' => InstallmentStatusEnum::ACTIVE, 'starts_in' => Carbon::today()]);
        }

        (new PopulateDebtSmsJobs)->handle($this->_balanceServiceMock);

        $dispatchedJobs = Bus::dispatched(SendDebtSmsJob::class);
        $this->assertCount(1, $dispatchedJobs);
        /** @var SendDebtSmsJob $job */
        foreach ($dispatchedJobs as $job) {
            $this->assertEquals(1, $job->installmentId);
            $this->assertEquals('sms', $job->queue);
            $this->assertEquals(0, $job->delay);
        }
    }

    #[Test]
    public function doNotPopulateInactiveInstallments(): void
    {
        $this->_balanceServiceMock->shouldReceive('isAvailableCompany')->twice()->andReturn(true);
        Installment::factory()->create(['status' => InstallmentStatusEnum::INACTIVE]);
        (new PopulateDebtSmsJobs)->handle($this->_balanceServiceMock);
        Bus::assertNotDispatched(SendDebtSmsJob::class);
    }

    #[Test]
    public function doNotPopulateIfThereIsNoAvailableTemplates(): void
    {
        $this->_balanceServiceMock->shouldReceive('isAvailableCompany')->twice()->andReturn(true);
        SmsTemplate::debtTemplates()->update(['status' => SmsTemplateStatusEnum::INACTIVE]);
        Installment::factory()->create(['status' => InstallmentStatusEnum::ACTIVE, 'starts_in' => Carbon::today()->subDay()]);
        Installment::factory()->create(['status' => InstallmentStatusEnum::ACTIVE, 'starts_in' => Carbon::today()]);
        Installment::factory()->create(['status' => InstallmentStatusEnum::ACTIVE, 'starts_in' => Carbon::today()->addDay()]);

        (new PopulateDebtSmsJobs)->handle($this->_balanceServiceMock);

        Bus::assertNotDispatched(SendDebtSmsJob::class);
    }
}
