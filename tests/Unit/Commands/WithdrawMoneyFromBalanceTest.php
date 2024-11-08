<?php

namespace Tests\Unit\Commands;

use App\Enums\Company\CompanyStatusEnum;
use App\Enums\ModuleEnum;
use App\Models\Company;
use App\Models\Company\BalanceCharge;
use App\Models\Company\Module;
use App\Services\User\BalanceService;
use Carbon\Carbon;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class WithdrawMoneyFromBalanceTest extends TestCase
{
    protected bool $tenancy = true;

    private Company $company;

    protected function setUp(): void
    {
        parent::setUp();
        $this->company = Company::query()->first();
    }

    #[Test]
    public function anotherDayWithdrawMoneyFromBalance()
    {
        $this->company->update(['payment_day' => Carbon::now()->day - 1]);
        Module::query()->where('name', ModuleEnum::INSTALLMENT)->update([
            'is_active' => true,
            'original_amount' => 50000,
        ]);
        $this->artisan('money:withdraw-balance');
        $balanceCharge = BalanceCharge::query()->first();
        $this->assertEmpty($balanceCharge);
        $this->assertEquals(0, BalanceService::getBalance());
    }

    #[Test]
    public function successWithdrawMoneyFromBalance()
    {
        $this->company->update(['payment_day' => Carbon::now()->day]);
        Module::query()->where('name', ModuleEnum::MULTIBRANCH_4)->update([
            'is_active' => true,
            'original_amount' => 500000,
        ]);
        $this->artisan('money:withdraw-balance');
        $balanceCharge = BalanceCharge::query()->first();
        $this->assertEquals(500000, $balanceCharge->amount);
        $this->assertEquals(Module::class, $balanceCharge->chargeable_type);
        $this->assertEquals(-500000, BalanceService::getBalance());
        $this->assertEquals(CompanyStatusEnum::INACTIVE, $this->company->refresh()->status);

    }
}
