<?php

namespace Tests\Unit\Jobs;

use App\Clients\UniredClient;
use App\Enums\Company\InstallmentStatusEnum;
use App\Jobs\CreateOrUpdateCustomerUniredCardsJob;
use App\Models\CentralCustomer;
use App\Models\CentralUniredCard;
use App\Models\Company\Customer;
use App\Models\Company\Installment;
use App\Services\UniredService;
use App\Services\User\BalanceService;
use PHPUnit\Framework\Attributes\Test;
use Tests\Fakers\UniredFaker;
use Tests\TestCase;

class CreateOrUpdateCustomerUniredCardsJobTest extends TestCase
{
    protected bool $tenancy = true;

    private $_balanceServiceMock;

    protected function setUp(): void
    {
        parent::setUp();

        CentralCustomer::truncate();
        CentralUniredCard::truncate();

        $this->_balanceServiceMock = $this->mock(BalanceService::class);
    }

    #[Test]
    public function successCreatingCustomerUniredCards(): void
    {
        UniredFaker::getCards();
        $this->_balanceServiceMock->expects()->isAvailableCompany()->twice()->andReturn(true);

        $customer = Customer::factory()->create();
        Installment::factory()->create(['customer_id' => $customer->id, 'status' => InstallmentStatusEnum::ACTIVE]);

        $job = new CreateOrUpdateCustomerUniredCardsJob($customer->id);

        $job->handle(new UniredService(new UniredClient), $this->_balanceServiceMock);

        $centralCustomer = $customer->getOrCreateCentralCustomer();
        $this->assertNotNull($centralCustomer);
        $this->assertCount(2, $centralCustomer->uniredCards);
    }

    #[Test]
    public function successUpdatingCustomerUniredCards(): void
    {
        UniredFaker::getCards();
        $this->_balanceServiceMock->expects()->isAvailableCompany()->times(3)->andReturn(true);

        $customer = Customer::factory()->create();
        Installment::factory()->create(['customer_id' => $customer->id, 'status' => InstallmentStatusEnum::ACTIVE]);

        $job = new CreateOrUpdateCustomerUniredCardsJob($customer->id);

        $job->handle(new UniredService(new UniredClient), $this->_balanceServiceMock);

        $job->handle(new UniredService(new UniredClient), $this->_balanceServiceMock);

        $centralCustomer = $customer->getOrCreateCentralCustomer();
        $this->assertNotNull($centralCustomer);
        $this->assertCount(2, $centralCustomer->uniredCards);
    }
}
