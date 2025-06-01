<?php

namespace Tests\Unit\Jobs;

use App\Enums\Company\CompanySettingsEnum;
use App\Enums\Company\InstallmentStatusEnum;
use App\Enums\FeatureEnum;
use App\Enums\Main\SettingsTypeEnum;
use App\Jobs\InstallmentChargeViaAtmosPinflJob;
use App\Models\Company\CompanySettings;
use App\Models\Company\Customer;
use App\Models\Company\Feature;
use App\Models\Company\Installment;
use App\Services\AtmosService;
use App\Services\User\BalanceService;
use Illuminate\Support\Facades\Bus;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class InstallmentChargeViaAtmosPinflJobTest extends TestCase
{
    protected bool $tenancy = true;

    protected function setUp(): void
    {
        parent::setUp();

        Bus::fake();
    }

    #[Test]
    public function dontChargeIfNotNeed(): void
    {
        $installment = Installment::factory()->create([
            'status' => InstallmentStatusEnum::ACTIVE,
            'starts_in' => now()->addDay(),
            'amount' => 100_000,
            'period' => 1,
            'pinfl_after_due_days' => 0,
            'guarantor_pinfl_after_due_days' => 0,
            'any_card_charge' => true,
        ]);

        $mock = $this->mock(AtmosService::class);
        $mock->expects()->withdrawAmountAndCreatePINFLTransaction()->times(0); // pinfl payment must not be run
        $this->mockBalanceService();

        (new InstallmentChargeViaAtmosPinflJob($installment->id))->handle($mock);
    }

    #[Test]
    public function dontChargeDueDays(): void
    {
        $installment = Installment::factory()->create([
            'status' => InstallmentStatusEnum::ACTIVE,
            'starts_in' => now(),
            'amount' => 100_000,
            'period' => 1,
            'pinfl_after_due_days' => 1,
            'guarantor_pinfl_after_due_days' => 1,
            'any_card_charge' => true,
        ]);

        $mock = $this->mock(AtmosService::class);
        $mock->expects()->withdrawAmountAndCreatePINFLTransaction()->times(0); // pinfl payment must not be ru
        $this->mockBalanceService();

        (new InstallmentChargeViaAtmosPinflJob($installment->id))->handle($mock);
    }

    #[Test]
    public function dontChargeDueDaysFromConfig(): void
    {
        $installment = Installment::factory()->create([
            'status' => InstallmentStatusEnum::ACTIVE,
            'starts_in' => now(),
            'amount' => 100_000,
            'period' => 1,
            'pinfl_after_due_days' => null,
            'guarantor_pinfl_after_due_days' => null,
            'any_card_charge' => true,
        ]);
        CompanySettings::query()->updateOrCreate(['name' => CompanySettingsEnum::PINFL_AFTER_DUE_DAYS], ['value' => 1, 'type' => SettingsTypeEnum::INT]);
        CompanySettings::query()->updateOrCreate(['name' => CompanySettingsEnum::GUARANTOR_PINFL_AFTER_DUE_DAYS], ['value' => 1, 'type' => SettingsTypeEnum::INT]);

        $mock = $this->mock(AtmosService::class);
        $mock->expects()->withdrawAmountAndCreatePINFLTransaction()->times(0); // pinfl payment must not be run
        $this->mockBalanceService();

        (new InstallmentChargeViaAtmosPinflJob($installment->id))->handle($mock);
    }

    #[Test]
    public function fullChargeFromCustomerAndGuarantor(): void
    {
        $installment = Installment::factory()->create([
            'status' => InstallmentStatusEnum::ACTIVE,
            'starts_in' => now(),
            'amount' => 100_000,
            'period' => 1,
            'pinfl_after_due_days' => 0,
            'guarantor_pinfl_after_due_days' => 0,
            'any_card_charge' => true,
        ]);
        $guarantor = Customer::factory()->create();
        $installment->guarantors()->attach($guarantor, ['any_card_charge' => true]);

        $mock = $this->mock(AtmosService::class);
        $mock->expects()->withdrawAmountAndCreatePINFLTransaction()->twice()->withAnyArgs(); // one for customer, second for guarantor
        $this->mockBalanceService();

        (new InstallmentChargeViaAtmosPinflJob($installment->id))->handle($mock);
    }

    #[Test]
    public function doesntChargeByPinflIfNotEnabled(): void
    {
        $installment = Installment::factory()->create([
            'status' => InstallmentStatusEnum::ACTIVE,
            'starts_in' => now(),
            'amount' => 100_000,
            'period' => 1,
            'pinfl_after_due_days' => 0,
            'guarantor_pinfl_after_due_days' => 0,
            'any_card_charge' => false,
        ]);

        $mock = $this->mock(AtmosService::class);
        $mock->expects()->withdrawAmountAndCreatePINFLTransaction()->times(0); // pinfl payment must not be run
        $this->mockBalanceService();

        (new InstallmentChargeViaAtmosPinflJob($installment->id))->handle($mock);
    }

    #[Test]
    public function doesntChargeByGuarantorIfNotDueDay(): void
    {
        $installment = Installment::factory()->create([
            'status' => InstallmentStatusEnum::ACTIVE,
            'starts_in' => now(),
            'amount' => 100_000,
            'period' => 1,
            'pinfl_after_due_days' => 0,
            'guarantor_pinfl_after_due_days' => 1,
            'any_card_charge' => true,
        ]);

        $mock = $this->mock(AtmosService::class);
        $mock->expects()->withdrawAmountAndCreatePINFLTransaction()->once()->withAnyArgs(); // one for customer
        $this->mockBalanceService();

        (new InstallmentChargeViaAtmosPinflJob($installment->id))->handle($mock);
    }

    #[Test]
    public function triesToChargeWithExactAmountByPinfl(): void
    {
        $installment = Installment::factory()->create([
            'status' => InstallmentStatusEnum::ACTIVE,
            'starts_in' => now(),
            'amount' => 100_000,
            'period' => 1,
            'percent' => 0,
            'initial_fee' => 0,
            'pinfl_after_due_days' => 0,
            'guarantor_pinfl_after_due_days' => 1,
            'any_card_charge' => true,
        ]);

        $mock = $this->mock(AtmosService::class);
        $mock->expects()->withdrawAmountAndCreateCardTransaction()->times(0);
        $mock->shouldReceive('withdrawAmountAndCreatePINFLTransaction')->once()->withArgs(fn ($card, $installment, $amount) => $amount === 100000.0);
        $this->mockBalanceService();

        (new InstallmentChargeViaAtmosPinflJob($installment->id))->handle($mock);
    }

    #[Test]
    public function triesToChargeWithExactAmountByPinflFromGuarantor(): void
    {
        $installment = Installment::factory()->create([
            'status' => InstallmentStatusEnum::ACTIVE,
            'starts_in' => now(),
            'amount' => 100_000,
            'period' => 1,
            'percent' => 0,
            'initial_fee' => 0,
            'pinfl_after_due_days' => 1,
            'guarantor_pinfl_after_due_days' => 0,
            'any_card_charge' => true,
        ]);
        $guarantor = Customer::factory()->create();
        $installment->guarantors()->attach($guarantor, ['any_card_charge' => true]);

        $mock = $this->mock(AtmosService::class);
        $mock->shouldReceive('withdrawAmountAndCreatePINFLTransaction')->once()->withArgs(fn ($card, $installment, $amount) => $amount === 100000.0);
        $this->mockBalanceService();

        (new InstallmentChargeViaAtmosPinflJob($installment->id))->handle($mock);
    }

    #[Test]
    public function doNotRunIfFeatureIsInactive(): void
    {
        $installment = Installment::factory()->create([
            'status' => InstallmentStatusEnum::ACTIVE,
            'starts_in' => now(),
            'amount' => 100_000,
            'period' => 1,
            'percent' => 0,
            'initial_fee' => 0,
            'pinfl_after_due_days' => 0,
            'guarantor_pinfl_after_due_days' => 0,
            'any_card_charge' => true,
        ]);

        $mock = $this->mock(AtmosService::class);
        $mock->shouldReceive('withdrawAmountAndCreatePINFLTransaction')->times(0);
        $this->mockBalanceService(0);

        // Disable the feature
        Feature::findByName(FeatureEnum::INSTALLMENT_AUTO_CHARGE_BY_PINFL)->update(['is_active' => false]);
        Feature::findByName(FeatureEnum::ATMOS_PINFL_PAY)->update(['is_active' => true]);

        (new InstallmentChargeViaAtmosPinflJob($installment->id))->handle($mock);

        // Disable the feature
        Feature::findByName(FeatureEnum::INSTALLMENT_AUTO_CHARGE_BY_PINFL)->update(['is_active' => true]);
        Feature::findByName(FeatureEnum::ATMOS_PINFL_PAY)->update(['is_active' => false]);
        $this->mockBalanceService(0);

        (new InstallmentChargeViaAtmosPinflJob($installment->id))->handle($mock);
    }

    private function mockBalanceService(int $callCount = 1, bool $returnValue = true): void
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
