<?php

namespace Tests\Unit\Jobs;

use App\Enums\Company\CompanySettingsEnum;
use App\Enums\Company\InstallmentStatusEnum;
use App\Enums\FeatureEnum;
use App\Jobs\SearchCustomerCardInUniredJob;
use App\Models\Company\CompanySettings;
use App\Models\Company\Feature;
use App\Models\Company\Installment;
use App\Models\Company\InstallmentGuarantor;
use App\Services\UniredService;
use App\Services\User\BalanceService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Bus;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SearchCustomerCardInUniredJobTest extends TestCase
{
    protected bool $tenancy = true;

    private UniredService $_uniredServiceMock;
    private BalanceService $_balanceServiceMock;
    private array $_installmentInput;

    protected function setUp(): void
    {
        parent::setUp();

        Bus::fake();

        $this->_uniredServiceMock = $this->mock(UniredService::class);
        $this->_balanceServiceMock = $this->mock(BalanceService::class);

        $this->_installmentInput = [
            'status' => InstallmentStatusEnum::ACTIVE,
            'any_card_charge' => true,
            'period' => 1,
            'amount' => 50_000,
            'paid_amount' => 0,
            'initial_fee' => 0,
            'percent' => 0,
            'starts_in' => Carbon::now()->subDay(),
            'pinfl_after_due_days' => 0,
            'guarantor_pinfl_after_due_days' => 0,
        ];
    }

    #[Test]
    public function successfulCardSearchAndCharge(): void
    {
        $installment = Installment::factory()->make($this->_installmentInput);
        $installment->saveQuietly();
        InstallmentGuarantor::factory()->create(['installment_id' => $installment->id, 'any_card_charge' => true]);

        $this->_uniredServiceMock->shouldReceive('updatePinflCardsList')->twice()->andReturn(true);
        $this->_uniredServiceMock->shouldReceive('getAndSyncPinflCards')->twice()->andReturn(true);

        $this->_balanceServiceMock->shouldReceive('isAvailableCompany')->once()->andReturn(true);
        $this->_balanceServiceMock->shouldReceive('chargeAmount')->twice();

        (new SearchCustomerCardInUniredJob($installment->id))->handle($this->_uniredServiceMock, $this->_balanceServiceMock);
    }

    #[Test]
    public function doesNotSearchOrChargeWhenFeatureInactive(): void
    {
        $feature = Feature::findByName(FeatureEnum::UNIRED_PINFL_PAY);
        $feature->update(['is_active' => false]);

        $installment = Installment::factory()->make($this->_installmentInput);
        $installment->saveQuietly();
        InstallmentGuarantor::factory()->create(['installment_id' => $installment->id, 'any_card_charge' => true]);

        $this->_uniredServiceMock->shouldReceive('updatePinflCardsList')->times(0);
        $this->_uniredServiceMock->shouldReceive('getAndSyncPinflCards')->times(0);

        $this->_balanceServiceMock->shouldReceive('isAvailableCompany')->times(0);
        $this->_balanceServiceMock->shouldReceive('chargeAmount')->times(0);

        (new SearchCustomerCardInUniredJob($installment->id))->handle($this->_uniredServiceMock, $this->_balanceServiceMock);
    }

    #[Test]
    public function doesNotSearchOrChargeWhenCompanyIsNotAvailable(): void
    {
        $installment = Installment::factory()->make($this->_installmentInput);
        $installment->saveQuietly();
        InstallmentGuarantor::factory()->create(['installment_id' => $installment->id, 'any_card_charge' => true]);

        $this->_balanceServiceMock->shouldReceive('isAvailableCompany')->once()->andReturn(false);
        $this->_balanceServiceMock->shouldReceive('chargeAmount')->times(0);

        $this->_uniredServiceMock->shouldReceive('updatePinflCardsList')->times(0);
        $this->_uniredServiceMock->shouldReceive('getAndSyncPinflCards')->times(0);

        (new SearchCustomerCardInUniredJob($installment->id))->handle($this->_uniredServiceMock, $this->_balanceServiceMock);
    }

    #[Test]
    public function doesNotSearchOrChargeWhenDebtBelowMinimum(): void
    {
        CompanySettings::query()->firstWhere('name', CompanySettingsEnum::PINFL_CARDS_UPDATE_MIN_DEBT)->update(['value' => 100_000]);

        $installment = Installment::factory()->make($this->_installmentInput);
        $installment->saveQuietly();
        InstallmentGuarantor::factory()->create(['installment_id' => $installment->id, 'any_card_charge' => true]);

        $this->_balanceServiceMock->shouldReceive('isAvailableCompany')->once()->andReturn(false);
        $this->_balanceServiceMock->shouldReceive('chargeAmount')->times(0);

        $this->_uniredServiceMock->shouldReceive('updatePinflCardsList')->times(0);
        $this->_uniredServiceMock->shouldReceive('getAndSyncPinflCards')->times(0);

        (new SearchCustomerCardInUniredJob($installment->id))->handle($this->_uniredServiceMock, $this->_balanceServiceMock);
    }

    #[Test]
    public function doesNotSearchOrChargeWhenPinflPaymentsIsNotAvailableYet(): void
    {
        $installment = Installment::factory()->make(
            array_merge($this->_installmentInput, [
                'pinfl_after_due_days' => 1,
                'guarantor_pinfl_after_due_days' => 1,
            ])
        );
        $installment->saveQuietly();
        InstallmentGuarantor::factory()->create(['installment_id' => $installment->id, 'any_card_charge' => true]);

        $this->_balanceServiceMock->shouldReceive('isAvailableCompany')->once()->andReturn(false);
        $this->_balanceServiceMock->shouldReceive('chargeAmount')->times(0);

        $this->_uniredServiceMock->shouldReceive('updatePinflCardsList')->times(0);
        $this->_uniredServiceMock->shouldReceive('getAndSyncPinflCards')->times(0);

        (new SearchCustomerCardInUniredJob($installment->id))->handle($this->_uniredServiceMock, $this->_balanceServiceMock);
    }
}
