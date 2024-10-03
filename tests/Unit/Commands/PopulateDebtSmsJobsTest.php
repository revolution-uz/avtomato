<?php

namespace Tests\Unit\Commands;

use App\Console\Commands\PopulateDebtSmsJobs;
use App\Enums\Company\CompanySettingsEnum;
use App\Enums\Company\InstallmentStatusEnum;
use App\Jobs\SendDebtSmsJob;
use App\Models\Company\CompanySettings;
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

    private $_balanceServiceMock;

    protected function setUp(): void
    {
        parent::setUp();

        CompanySettings::where('name', CompanySettingsEnum::SMS_INSTALLMENT_PAYMENT)->update(['value' => '1']);

        $this->_balanceServiceMock = $this->mock(BalanceService::class);
        $this->_balanceServiceMock->shouldReceive('isBalanceEnough')->once()->andReturn(true);

        for ($i = -1; $i <= 1; $i++) {
            SmsTemplate::query()->create([
                'original_template' => 'Test',
                'data' => [
                    'category' => 'debt-templates',
                    'overdue_days' => $i,
                    'is_active' => true,
                ],
            ]);
        }
    }

    #[Test]
    public function populateCorrectDebtorInstallmentsForToday(): void
    {
        Bus::fake();

        /** @var Installment $installment */
        $installment = Installment::factory()->create(['status' => InstallmentStatusEnum::ACTIVE, 'starts_in' => Carbon::now()]);

        (new PopulateDebtSmsJobs)->handle($this->_balanceServiceMock);

        Bus::assertDispatched(
            SendDebtSmsJob::class,
            fn ($job) => $job->installmentId === $installment->id and $job->queue === 'debt-sms' and $job->delay === 0
        );
    }

    #[Test]
    public function populateCorrectDebtorInstallmentsForYesterday(): void
    {
        Bus::fake();

        /** @var Installment $installment */
        $installment = Installment::factory()->create(['status' => InstallmentStatusEnum::ACTIVE, 'starts_in' => Carbon::now()->subDay()]);

        (new PopulateDebtSmsJobs)->handle($this->_balanceServiceMock);

        Bus::assertDispatched(
            SendDebtSmsJob::class,
            fn ($job) => $job->installmentId === $installment->id and $job->queue === 'debt-sms' and $job->delay === 0
        );
    }

    #[Test]
    public function populateCorrectDebtorInstallmentsForTomorrow(): void
    {
        Bus::fake();

        /** @var Installment $installment */
        $installment = Installment::factory()->create(['status' => InstallmentStatusEnum::ACTIVE, 'starts_in' => Carbon::now()->addDay()]);

        (new PopulateDebtSmsJobs)->handle($this->_balanceServiceMock);

        Bus::assertDispatched(
            SendDebtSmsJob::class,
            fn ($job) => $job->installmentId === $installment->id and $job->queue === 'debt-sms' and $job->delay === 0
        );
    }

    #[Test]
    public function doNotPopulateInactiveInstallments(): void
    {
        Bus::fake();
        Installment::factory()->create(['status' => InstallmentStatusEnum::INACTIVE]);
        (new PopulateDebtSmsJobs)->handle($this->_balanceServiceMock);
        Bus::assertNotDispatched(SendDebtSmsJob::class);
    }

    #[Test]
    public function doNotPopulateIfThereIsNoAvailableTemplates(): void
    {
        SmsTemplate::debtTemplates()->update(['data->is_active' => false]);

        Bus::fake();

        Installment::factory()->create(['status' => InstallmentStatusEnum::ACTIVE, 'starts_in' => Carbon::today()->subDay()]);
        Installment::factory()->create(['status' => InstallmentStatusEnum::ACTIVE, 'starts_in' => Carbon::today()]);
        Installment::factory()->create(['status' => InstallmentStatusEnum::ACTIVE, 'starts_in' => Carbon::today()->addDay()]);

        (new PopulateDebtSmsJobs)->handle($this->_balanceServiceMock);

        Bus::assertNotDispatched(SendDebtSmsJob::class);
    }
}
