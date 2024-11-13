<?php

namespace Tests\Unit\Jobs;

use App\Enums\Company\CompanySettingsEnum;
use App\Enums\Company\InstallmentStatusEnum;
use App\Enums\Main\SettingsTypeEnum;
use App\Jobs\CreateOrUpdateInstallmentInUniredJob;
use App\Models\Company\CompanySettings;
use App\Models\Company\Customer;
use App\Models\Company\Installment;
use App\Services\UniredService;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ImportInstallmentIntoUniredJobTest extends TestCase
{
    protected bool $tenancy = true;

    #[Test]
    public function dontChargeIfNotNeed(): void
    {
        $installment = Installment::factory()->create([
            'status' => InstallmentStatusEnum::ACTIVE,
            'starts_in' => now()->addDay(),
            'amount' => 1000000000,
            'period' => 1,
            'pinfl_after_due_days' => 0,
            'guarantor_pinfl_after_due_days' => 0,
            'any_card_charge' => true,
        ]);
        $job = new CreateOrUpdateInstallmentInUniredJob($installment->id);
        $mock = $this->mock(UniredService::class);
        $mock->expects()->createOrUpdateInstallment()->times(0);
        $mock->expects()->createOrUpdateInstallmentGuarantor()->times(0);
        $job->handle($mock);
    }

    #[Test]
    public function dontChargeDueDays(): void
    {
        $installment = Installment::factory()->create([
            'status' => InstallmentStatusEnum::ACTIVE,
            'starts_in' => now(),
            'amount' => 1000000000,
            'period' => 1,
            'pinfl_after_due_days' => 1,
            'guarantor_pinfl_after_due_days' => 1,
            'any_card_charge' => true,
        ]);
        $job = new CreateOrUpdateInstallmentInUniredJob($installment->id);
        $mock = $this->mock(UniredService::class);
        $mock->expects()->createOrUpdateInstallment()->times(0);
        $mock->expects()->createOrUpdateInstallmentGuarantor()->times(0);
        $job->handle($mock);
    }

    #[Test]
    public function dontChargeDueDaysFromConfig(): void
    {
        $installment = Installment::factory()->create([
            'status' => InstallmentStatusEnum::ACTIVE,
            'starts_in' => now(),
            'amount' => 1000000000,
            'period' => 1,
            'pinfl_after_due_days' => null,
            'guarantor_pinfl_after_due_days' => null,
            'any_card_charge' => true,
        ]);
        CompanySettings::query()->updateOrCreate(['name' => CompanySettingsEnum::PINFL_AFTER_DUE_DAYS], ['value' => 1, 'type' => SettingsTypeEnum::INT]);
        CompanySettings::query()->updateOrCreate(['name' => CompanySettingsEnum::GUARANTOR_PINFL_AFTER_DUE_DAYS], ['value' => 1, 'type' => SettingsTypeEnum::INT]);

        $job = new CreateOrUpdateInstallmentInUniredJob($installment->id);
        $mock = $this->mock(UniredService::class);
        $mock->expects()->createOrUpdateInstallment()->times(0);
        $mock->expects()->createOrUpdateInstallmentGuarantor()->times(0);

        $job->handle($mock);
    }

    #[Test]
    public function fullChargeFromCustomerAndGuarantor(): void
    {
        $installment = Installment::factory()->create([
            'status' => InstallmentStatusEnum::ACTIVE,
            'starts_in' => now(),
            'amount' => 1000000000,
            'period' => 1,
            'pinfl_after_due_days' => 0,
            'guarantor_pinfl_after_due_days' => 0,
            'any_card_charge' => true,
        ]);
        $guarantor = Customer::factory()->create();
        $installment->guarantors()->attach($guarantor, ['any_card_charge' => true]);

        $job = new CreateOrUpdateInstallmentInUniredJob($installment->id);
        $mock = $this->mock(UniredService::class);
        $mock->expects()->createOrUpdateInstallment()->once()->withAnyArgs();
        $mock->expects()->createOrUpdateInstallmentGuarantor()->once()->withAnyArgs();

        $job->handle($mock);
    }

    #[Test]
    public function doesntChargeByPinflIfNotEnabled(): void
    {
        $installment = Installment::factory()->create([
            'status' => InstallmentStatusEnum::ACTIVE,
            'starts_in' => now(),
            'amount' => 1000000000,
            'period' => 1,
            'pinfl_after_due_days' => 0,
            'guarantor_pinfl_after_due_days' => 0,
            'any_card_charge' => true,
        ]);
        $guarantor = Customer::factory()->create();
        $installment->guarantors()->attach($guarantor, ['any_card_charge' => false]);

        $job = new CreateOrUpdateInstallmentInUniredJob($installment->id);
        $mock = $this->mock(UniredService::class);
        $mock->expects()->createOrUpdateInstallment()->once()->withAnyArgs();
        $mock->expects()->createOrUpdateInstallmentGuarantor()->times(0);

        $job->handle($mock);
    }

    #[Test]
    public function doesntChargeByGuarantorIfNotDueDay(): void
    {
        $installment = Installment::factory()->create([
            'status' => InstallmentStatusEnum::ACTIVE,
            'starts_in' => now(),
            'amount' => 1000000000,
            'period' => 1,
            'pinfl_after_due_days' => 0,
            'guarantor_pinfl_after_due_days' => 1,
            'any_card_charge' => true,
        ]);
        $guarantor = Customer::factory()->create();
        $installment->guarantors()->attach($guarantor, ['any_card_charge' => true]);

        $job = new CreateOrUpdateInstallmentInUniredJob($installment->id);
        $mock = $this->mock(UniredService::class);
        $mock->expects()->createOrUpdateInstallment()->once()->withAnyArgs();
        $mock->expects()->createOrUpdateInstallmentGuarantor()->times(0);

        $job->handle($mock);
    }
}
