<?php

namespace Tests\Unit\Commands;

use App\Console\Commands\PopulateCreateOrUpdateCustomerUniredCardsJobs;
use App\Enums\Company\InstallmentStatusEnum;
use App\Enums\FeatureEnum;
use App\Jobs\CreateOrUpdateCustomerUniredCardsJob;
use App\Models\Company;
use App\Models\Company\Customer;
use App\Models\Company\Feature;
use App\Models\Company\Installment;
use App\Models\Company\InstallmentGuarantor;
use App\Services\User\BalanceService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Bus;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PopulateCreateOrUpdateCustomerUniredCardsJobsTest extends TestCase
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
    public function getsCorrectCustomers(): void
    {
        $this->_balanceServiceMock->shouldReceive('isAvailableCompany')->twice()->andReturn(true);

        $customer = Customer::factory()->create();
        $installment = Installment::factory()->create(['customer_id' => $customer->id, 'status' => InstallmentStatusEnum::ACTIVE]);

        $guarantor = Customer::factory()->create();
        InstallmentGuarantor::factory()->create(['installment_id' => $installment->id, 'customer_id' => $guarantor->id]);

        (new PopulateCreateOrUpdateCustomerUniredCardsJobs)->handle($this->_balanceServiceMock);

        $dispatchedJobs = Bus::dispatched(CreateOrUpdateCustomerUniredCardsJob::class);

        $this->assertCount(2, $dispatchedJobs);
        $this->assertEquals(CreateOrUpdateCustomerUniredCardsJob::JOB_ID . '-' . tenant('id') . '-' . $customer->id, $dispatchedJobs[0]->uniqueId());
        $this->assertEquals(CreateOrUpdateCustomerUniredCardsJob::JOB_ID, $dispatchedJobs[0]->queue);
        $this->assertTrue($dispatchedJobs[0]->delay instanceof Carbon);
        $this->assertGreaterThanOrEqual(0, $dispatchedJobs[0]->delay->diffInSeconds());
    }

    #[Test]
    public function dontPopulateJobsWithInactiveFeature(): void
    {
        $this->_balanceServiceMock->shouldReceive('isAvailableCompany')->times(0);
        foreach (Company::all() as $company) {
            tenancy()->initialize($company);
            Feature::findByName(FeatureEnum::UNIRED_PAY)->update(['is_active' => false]);
            Installment::factory()->create(['status' => InstallmentStatusEnum::ACTIVE]);
        }

        (new PopulateCreateOrUpdateCustomerUniredCardsJobs)->handle($this->_balanceServiceMock);

        Bus::assertNotDispatched(CreateOrUpdateCustomerUniredCardsJob::class);
    }

    #[Test]
    public function dontPopulateJobsWithInsufficientBalance(): void
    {
        $this->_balanceServiceMock->shouldReceive('isAvailableCompany')->twice()->andReturn(false);
        foreach (Company::all() as $company) {
            tenancy()->initialize($company);
            Feature::findByName(FeatureEnum::UNIRED_PAY)->update(['is_active' => true]);
            Installment::factory()->create(['status' => InstallmentStatusEnum::ACTIVE]);
        }

        (new PopulateCreateOrUpdateCustomerUniredCardsJobs)->handle($this->_balanceServiceMock);

        Bus::assertNotDispatched(CreateOrUpdateCustomerUniredCardsJob::class);
    }
}
