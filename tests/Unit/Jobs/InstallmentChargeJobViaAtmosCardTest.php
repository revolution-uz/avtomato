<?php

namespace Tests\Unit\Jobs;

use App\Enums\Company\CardStatusEnum;
use App\Enums\Company\CompanySettingsEnum;
use App\Enums\Company\InstallmentStatusEnum;
use App\Enums\FeatureEnum;
use App\Enums\Main\SettingsTypeEnum;
use App\Jobs\InstallmentChargeViaAtmosCardJob;
use App\Models\Company\Card;
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

class InstallmentChargeJobViaAtmosCardTest extends TestCase
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
            'amount' => 1000000000,
            'period' => 1,
            'card_after_due_days' => 0,
            'guarantor_after_due_days' => 0,
            'any_card_charge' => true,
        ]);
        $card = Card::factory()->create(['status' => CardStatusEnum::ACTIVE, 'customer_id' => $installment->customer_id]);
        $installment->cards()->attach($card);

        $mock = $this->mock(AtmosService::class);
        $mock->expects()->withdrawAmountAndCreateCardTransaction()->times(0); // card payment  must not be run
        $this->mockBalanceService();

        (new InstallmentChargeViaAtmosCardJob($installment->id))->handle($mock);
    }

    #[Test]
    public function dontChargeDueDays(): void
    {
        $installment = Installment::factory()->create([
            'status' => InstallmentStatusEnum::ACTIVE,
            'starts_in' => now(),
            'amount' => 1000000000,
            'period' => 1,
            'card_after_due_days' => 1,
            'guarantor_after_due_days' => 1,
            'any_card_charge' => true,
        ]);
        $card = Card::factory()->create(['status' => CardStatusEnum::ACTIVE, 'customer_id' => $installment->customer_id]);
        $installment->cards()->attach($card);

        $mock = $this->mock(AtmosService::class);
        $mock->expects()->withdrawAmountAndCreateCardTransaction()->times(0); // card payment  must not be run
        $this->mockBalanceService();

        (new InstallmentChargeViaAtmosCardJob($installment->id))->handle($mock);
    }

    #[Test]
    public function dontChargeDueDaysFromConfig(): void
    {
        $installment = Installment::factory()->create([
            'status' => InstallmentStatusEnum::ACTIVE,
            'starts_in' => now(),
            'amount' => 1000000000,
            'period' => 1,
            'card_after_due_days' => null,
            'guarantor_after_due_days' => null,
            'any_card_charge' => true,
        ]);
        $card = Card::factory()->create(['status' => CardStatusEnum::ACTIVE, 'customer_id' => $installment->customer_id]);
        $installment->cards()->attach($card);
        CompanySettings::query()->updateOrCreate(['name' => CompanySettingsEnum::CARD_AFTER_DUE_DAYS], ['value' => 1, 'type' => SettingsTypeEnum::INT]);
        CompanySettings::query()->updateOrCreate(['name' => CompanySettingsEnum::GUARANTOR_AFTER_DUE_DAYS], ['value' => 1, 'type' => SettingsTypeEnum::INT]);

        $mock = $this->mock(AtmosService::class);
        $mock->expects()->withdrawAmountAndCreateCardTransaction()->times(0); // card payment  must not be run
        $this->mockBalanceService();

        (new InstallmentChargeViaAtmosCardJob($installment->id))->handle($mock);
    }

    #[Test]
    public function fullChargeFromCustomerAndGuarantor(): void
    {
        $installment = Installment::factory()->create([
            'status' => InstallmentStatusEnum::ACTIVE,
            'starts_in' => now(),
            'amount' => 1000000000,
            'period' => 1,
            'card_after_due_days' => 0,
            'guarantor_after_due_days' => 0,
            'any_card_charge' => true,
        ]);
        $customerCard = Card::factory()->create(['status' => CardStatusEnum::ACTIVE, 'customer_id' => $installment->customer_id]);
        $guarantor = Customer::factory()->create();
        $guarantorCard = Card::factory()->create(['status' => CardStatusEnum::ACTIVE, 'customer_id' => $guarantor->id]);
        $installment->guarantors()->attach($guarantor, ['any_card_charge' => true]);
        $installment->cards()->attach($customerCard);
        $installment->cards()->attach($guarantorCard);

        $mock = $this->mock(AtmosService::class);
        $mock->expects()->withdrawAmountAndCreateCardTransaction()->twice()->withAnyArgs(); // one for customer, second for guarantor
        $this->mockBalanceService();

        (new InstallmentChargeViaAtmosCardJob($installment->id))->handle($mock);
    }

    #[Test]
    public function fullChargeFromCustomerAndGuarantorByConfig(): void
    {
        $installment = Installment::factory()->create([
            'status' => InstallmentStatusEnum::ACTIVE,
            'starts_in' => now(),
            'amount' => 1000000000,
            'period' => 1,
            'card_after_due_days' => null,
            'guarantor_after_due_days' => null,
            'any_card_charge' => true,
        ]);
        $customerCard = Card::factory()->create(['status' => CardStatusEnum::ACTIVE, 'customer_id' => $installment->customer_id]);
        $guarantor = Customer::factory()->create();
        $guarantorCard = Card::factory()->create(['status' => CardStatusEnum::ACTIVE, 'customer_id' => $guarantor->id]);
        $installment->guarantors()->attach($guarantor, ['any_card_charge' => true]);
        $installment->cards()->attach($customerCard);
        $installment->cards()->attach($guarantorCard);
        CompanySettings::query()->updateOrCreate(['name' => CompanySettingsEnum::CARD_AFTER_DUE_DAYS], ['value' => 0, 'type' => SettingsTypeEnum::INT]);
        CompanySettings::query()->updateOrCreate(['name' => CompanySettingsEnum::GUARANTOR_AFTER_DUE_DAYS], ['value' => 0, 'type' => SettingsTypeEnum::INT]);

        $mock = $this->mock(AtmosService::class);
        $mock->expects()->withdrawAmountAndCreateCardTransaction()->twice()->withAnyArgs(); // one for customer, second for guarantor
        $this->mockBalanceService();

        (new InstallmentChargeViaAtmosCardJob($installment->id))->handle($mock);
    }

    #[Test]
    public function doesntChargeByGuarantorIfNotDueDay(): void
    {
        $installment = Installment::factory()->create([
            'status' => InstallmentStatusEnum::ACTIVE,
            'starts_in' => now(),
            'amount' => 1000000000,
            'period' => 1,
            'card_after_due_days' => 0,
            'guarantor_after_due_days' => 1,
            'any_card_charge' => true,
        ]);
        $customerCard = Card::factory()->create(['status' => CardStatusEnum::ACTIVE, 'customer_id' => $installment->customer_id]);
        $guarantor = Customer::factory()->create();
        $guarantorCard = Card::factory()->create(['status' => CardStatusEnum::ACTIVE, 'customer_id' => $guarantor->id]);
        $installment->guarantors()->attach($guarantor, ['any_card_charge' => true]);
        $installment->cards()->attach($customerCard);
        $installment->cards()->attach($guarantorCard);

        $mock = $this->mock(AtmosService::class);
        $mock->expects()->withdrawAmountAndCreateCardTransaction()->once()->withAnyArgs(); // one for customer
        $this->mockBalanceService();

        (new InstallmentChargeViaAtmosCardJob($installment->id))->handle($mock);
    }

    #[Test]
    public function triesToChargeByCard(): void
    {
        $installment = Installment::factory()->create([
            'status' => InstallmentStatusEnum::ACTIVE,
            'starts_in' => now(),
            'amount' => 1000000000,
            'period' => 1,
            'card_after_due_days' => 0,
            'guarantor_after_due_days' => 2,
            'any_card_charge' => true,
        ]);
        $card = Card::factory()->create(['status' => CardStatusEnum::ACTIVE, 'customer_id' => $installment->customer_id]);
        $installment->cards()->attach($card);

        $mock = $this->mock(AtmosService::class);
        $mock->expects()->withdrawAmountAndCreateCardTransaction()->once()->withAnyArgs();
        $this->mockBalanceService();

        (new InstallmentChargeViaAtmosCardJob($installment->id))->handle($mock);
    }

    #[Test]
    public function triesToChargeByTwoCards(): void
    {
        $installment = Installment::factory()->create([
            'status' => InstallmentStatusEnum::ACTIVE,
            'starts_in' => now(),
            'amount' => 1000000000,
            'period' => 1,
            'card_after_due_days' => 0,
            'guarantor_after_due_days' => 1,
            'any_card_charge' => true,
        ]);
        $cards = Card::factory()->count(2)->create(['status' => CardStatusEnum::ACTIVE, 'customer_id' => $installment->customer_id]);
        $installment->cards()->attach($cards);

        $mock = $this->mock(AtmosService::class);
        $mock->expects()->withdrawAmountAndCreateCardTransaction()->twice()->withAnyArgs();
        $this->mockBalanceService();

        (new InstallmentChargeViaAtmosCardJob($installment->id))->handle($mock);
    }

    #[Test]
    public function triesToChargeWithExactAmountByCard(): void
    {
        $installment = Installment::factory()->create([
            'status' => InstallmentStatusEnum::ACTIVE,
            'starts_in' => now(),
            'amount' => 100000,
            'period' => 1,
            'percent' => 0,
            'initial_fee' => 0,
            'card_after_due_days' => 0,
            'guarantor_after_due_days' => 1,
            'any_card_charge' => true,
        ]);
        $card = Card::factory()->create(['status' => CardStatusEnum::ACTIVE, 'customer_id' => $installment->customer_id]);
        $installment->cards()->attach($card);
        $guarantor = Customer::factory()->create();
        $installment->guarantors()->attach($guarantor, ['any_card_charge' => true]);
        $guarantorCard = Card::factory()->create(['status' => CardStatusEnum::ACTIVE, 'customer_id' => $guarantor->id]);
        $installment->cards()->attach($guarantorCard);

        $mock = $this->mock(AtmosService::class);
        $mock->shouldReceive('withdrawAmountAndCreateCardTransaction')->once()->withArgs(fn ($card, $installment, $amount) => $amount === 100000.0);
        $this->mockBalanceService();

        (new InstallmentChargeViaAtmosCardJob($installment->id))->handle($mock);
    }

    #[Test]
    public function triesToChargeWithExactAmountByCardFromGuarantor(): void
    {
        $installment = Installment::factory()->create([
            'status' => InstallmentStatusEnum::ACTIVE,
            'starts_in' => now(),
            'amount' => 100000,
            'period' => 1,
            'percent' => 0,
            'initial_fee' => 0,
            'card_after_due_days' => 1,
            'guarantor_after_due_days' => 0,
            'any_card_charge' => true,
        ]);
        $customerCard = Card::factory()->create(['status' => CardStatusEnum::ACTIVE, 'customer_id' => $installment->customer_id]);
        $installment->cards()->attach($customerCard);
        $guarantor = Customer::factory()->create();
        $guarantorCard = Card::factory()->create(['status' => CardStatusEnum::ACTIVE, 'customer_id' => $guarantor->id]);
        $installment->guarantors()->attach($guarantor, ['any_card_charge' => true]);
        $installment->cards()->attach($guarantorCard);

        $mock = $this->mock(AtmosService::class);
        $mock->shouldReceive('withdrawAmountAndCreateCardTransaction')->once()->withArgs(fn ($card, $installment, $amount) => $amount === 100000.0);
        $this->mockBalanceService();

        (new InstallmentChargeViaAtmosCardJob($installment->id))->handle($mock);
    }

    #[Test]
    public function doNotRunIfFeatureIsInactive(): void
    {
        $installment = Installment::factory()->create([
            'status' => InstallmentStatusEnum::ACTIVE,
            'starts_in' => now()->subDay(),
            'amount' => 100_000,
            'period' => 1,
            'card_after_due_days' => 0,
            'guarantor_after_due_days' => 0,
            'any_card_charge' => true,
        ]);
        $card = Card::factory()->create(['status' => CardStatusEnum::ACTIVE, 'customer_id' => $installment->customer_id]);
        $installment->cards()->attach($card);

        $mock = $this->mock(AtmosService::class);
        $mock->shouldReceive('withdrawAmountAndCreateCardTransaction')->times(0);
        $this->mockBalanceService(0);

        // Disable the feature
        Feature::findByName(FeatureEnum::INSTALLMENT_AUTO_CHARGE)->update(['is_active' => false]);
        Feature::findByName(FeatureEnum::ATMOS_CARD_PAY)->update(['is_active' => true]);

        (new InstallmentChargeViaAtmosCardJob($installment->id))->handle($mock);

        // Disable the feature
        Feature::findByName(FeatureEnum::INSTALLMENT_AUTO_CHARGE)->update(['is_active' => true]);
        Feature::findByName(FeatureEnum::ATMOS_CARD_PAY)->update(['is_active' => false]);
        $this->mockBalanceService(0);

        (new InstallmentChargeViaAtmosCardJob($installment->id))->handle($mock);
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
