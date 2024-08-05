<?php

namespace Tests\Unit\Services;

use App\Clients\AtmosBalanceClient;
use App\Enums\Company\CompanyStatusEnum;
use App\Models\Company\BalanceCharge;
use App\Models\Company\BalanceFund;
use App\Models\Company\Feature;
use App\Services\User\BalanceService;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BalanceServiceTest extends TestCase
{
    protected bool $tenancy = true;

    private BalanceService $_balanceService;

    private Feature $_feature;

    protected function setUp(): void
    {
        parent::setUp();

        BalanceFund::query()->forceDelete();
        BalanceCharge::query()->forceDelete();
        $this->_feature = Feature::query()->first();
        $this->_feature->updateOrFail(['amount', 50000]);
        tenant()->updateOrFail(['status' => CompanyStatusEnum::ACTIVE]);
        $this->_balanceService = new BalanceService(new AtmosBalanceClient);
    }

    #[Test]
    public function getBalanceWorking()
    {
        BalanceCharge::factory()->create(['amount' => 50000, 'feature_id' => $this->_feature->id]);
        $this->assertEquals(-50000, $this->_balanceService->getBalance());
        $this->assertEquals(CompanyStatusEnum::ACTIVE, tenant()->refresh()?->status);

        // Make company status inactive
        BalanceCharge::factory()->create(['amount' => 50000, 'feature_id' => $this->_feature->id]);
        $this->assertEquals(-100000, $this->_balanceService->getBalance());
        $this->assertEquals(CompanyStatusEnum::INACTIVE, tenant()->refresh()?->status);

        BalanceFund::factory()->create(['amount' => 50000]);
        $this->assertEquals(-50000, $this->_balanceService->getBalance());
        $this->assertEquals(CompanyStatusEnum::INACTIVE, tenant()->refresh()?->status);

        // Make company status active
        BalanceFund::factory()->create(['amount' => 50000]);
        $this->assertEquals(0, $this->_balanceService->getBalance());
        $this->assertEquals(CompanyStatusEnum::ACTIVE, tenant()->refresh()?->status);
    }
}
