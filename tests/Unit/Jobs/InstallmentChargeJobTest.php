<?php

namespace Tests\Unit\Jobs;

use App\Enums\Company\CardStatusEnum;
use App\Enums\Company\CompanySettingsEnum;
use App\Enums\Company\InstallmentStatusEnum;
use App\Enums\Main\SettingsTypeEnum;
use App\Jobs\InstallmentChargeJob;
use App\Models\Company\Card;
use App\Models\Company\CompanySettings;
use App\Models\Company\Customer;
use App\Models\Company\Installment;
use App\Services\AtmosService;
use Illuminate\Support\Facades\Bus;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class InstallmentChargeJobTest extends TestCase
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
            'pinfl_after_due_days' => 0,
            'guarantor_after_due_days' => 0,
            'guarantor_pinfl_after_due_days' => 0,
            'any_card_charge' => true,
        ]);
        $card = Card::factory()->create(['status' => CardStatusEnum::ACTIVE, 'customer_id' => $installment->customer_id]);
        $installment->cards()->attach($card);
        $job = new InstallmentChargeJob($installment->id);
        $mock = $this->mock(AtmosService::class);
        $mock->expects()->withdrawAmountAndCreateCardTransaction()->times(0); // card payment  must not be run
        $mock->expects()->withdrawAmountAndCreatePINFLTransaction()->times(0); // pinfl payment must not be run
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
            'card_after_due_days' => 1,
            'pinfl_after_due_days' => 1,
            'guarantor_after_due_days' => 1,
            'guarantor_pinfl_after_due_days' => 1,
            'any_card_charge' => true,
        ]);
        $card = Card::factory()->create(['status' => CardStatusEnum::ACTIVE, 'customer_id' => $installment->customer_id]);
        $installment->cards()->attach($card);
        $job = new InstallmentChargeJob($installment->id);
        $mock = $this->mock(AtmosService::class);
        $mock->expects()->withdrawAmountAndCreateCardTransaction()->times(0); // card payment  must not be run
        $mock->expects()->withdrawAmountAndCreatePINFLTransaction()->times(0); // pinfl payment must not be ru
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
            'card_after_due_days' => null,
            'pinfl_after_due_days' => null,
            'guarantor_after_due_days' => null,
            'guarantor_pinfl_after_due_days' => null,
            'any_card_charge' => true,
        ]);
        $card = Card::factory()->create(['status' => CardStatusEnum::ACTIVE, 'customer_id' => $installment->customer_id]);
        $installment->cards()->attach($card);
        CompanySettings::query()->updateOrCreate(['name' => CompanySettingsEnum::CARD_AFTER_DUE_DAYS], ['value' => 1, 'type' => SettingsTypeEnum::INT]);
        CompanySettings::query()->updateOrCreate(['name' => CompanySettingsEnum::PINFL_AFTER_DUE_DAYS], ['value' => 1, 'type' => SettingsTypeEnum::INT]);
        CompanySettings::query()->updateOrCreate(['name' => CompanySettingsEnum::GUARANTOR_AFTER_DUE_DAYS], ['value' => 1, 'type' => SettingsTypeEnum::INT]);
        CompanySettings::query()->updateOrCreate(['name' => CompanySettingsEnum::GUARANTOR_PINFL_AFTER_DUE_DAYS], ['value' => 1, 'type' => SettingsTypeEnum::INT]);

        $job = new InstallmentChargeJob($installment->id);
        $mock = $this->mock(AtmosService::class);
        $mock->expects()->withdrawAmountAndCreateCardTransaction()->times(0); // card payment  must not be run
        $mock->expects()->withdrawAmountAndCreatePINFLTransaction()->times(0); // pinfl payment must not be run

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
            'card_after_due_days' => 0,
            'pinfl_after_due_days' => 0,
            'guarantor_after_due_days' => 0,
            'guarantor_pinfl_after_due_days' => 0,
            'any_card_charge' => true,
        ]);
        $customerCard = Card::factory()->create(['status' => CardStatusEnum::ACTIVE, 'customer_id' => $installment->customer_id]);
        $guarantor = Customer::factory()->create();
        $guarantorCard = Card::factory()->create(['status' => CardStatusEnum::ACTIVE, 'customer_id' => $guarantor->id]);
        $installment->guarantors()->attach($guarantor, ['any_card_charge' => true]);
        $installment->cards()->attach($customerCard);
        $installment->cards()->attach($guarantorCard);

        $job = new InstallmentChargeJob($installment->id);
        $mock = $this->mock(AtmosService::class);
        $mock->expects()->withdrawAmountAndCreateCardTransaction()->twice()->withAnyArgs(); // one for customer, second for guarantor
        $mock->expects()->withdrawAmountAndCreatePINFLTransaction()->twice()->withAnyArgs(); // one for customer, second for guarantor

        $job->handle($mock);
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
            'pinfl_after_due_days' => null,
            'guarantor_after_due_days' => null,
            'guarantor_pinfl_after_due_days' => null,
            'any_card_charge' => true,
        ]);
        $customerCard = Card::factory()->create(['status' => CardStatusEnum::ACTIVE, 'customer_id' => $installment->customer_id]);
        $guarantor = Customer::factory()->create();
        $guarantorCard = Card::factory()->create(['status' => CardStatusEnum::ACTIVE, 'customer_id' => $guarantor->id]);
        $installment->guarantors()->attach($guarantor, ['any_card_charge' => true]);
        $installment->cards()->attach($customerCard);
        $installment->cards()->attach($guarantorCard);
        CompanySettings::query()->updateOrCreate(['name' => CompanySettingsEnum::CARD_AFTER_DUE_DAYS], ['value' => 0, 'type' => SettingsTypeEnum::INT]);
        CompanySettings::query()->updateOrCreate(['name' => CompanySettingsEnum::PINFL_AFTER_DUE_DAYS], ['value' => 0, 'type' => SettingsTypeEnum::INT]);
        CompanySettings::query()->updateOrCreate(['name' => CompanySettingsEnum::GUARANTOR_AFTER_DUE_DAYS], ['value' => 0, 'type' => SettingsTypeEnum::INT]);
        CompanySettings::query()->updateOrCreate(['name' => CompanySettingsEnum::GUARANTOR_PINFL_AFTER_DUE_DAYS], ['value' => 0, 'type' => SettingsTypeEnum::INT]);

        $job = new InstallmentChargeJob($installment->id);
        $mock = $this->mock(AtmosService::class);
        $mock->expects()->withdrawAmountAndCreateCardTransaction()->twice()->withAnyArgs(); // one for customer, second for guarantor
        $mock->expects()->withdrawAmountAndCreatePINFLTransaction()->twice()->withAnyArgs(); // one for customer, second for guarantor

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
            'card_after_due_days' => 0,
            'pinfl_after_due_days' => 0,
            'guarantor_after_due_days' => 0,
            'guarantor_pinfl_after_due_days' => 0,
            'any_card_charge' => false,
        ]);
        $customerCard = Card::factory()->create(['status' => CardStatusEnum::ACTIVE, 'customer_id' => $installment->customer_id]);
        $guarantor = Customer::factory()->create();
        $guarantorCard = Card::factory()->create(['status' => CardStatusEnum::ACTIVE, 'customer_id' => $guarantor->id]);
        $installment->guarantors()->attach($guarantor, ['any_card_charge' => false]);
        $installment->cards()->attach($customerCard);
        $installment->cards()->attach($guarantorCard);

        $job = new InstallmentChargeJob($installment->id);
        $mock = $this->mock(AtmosService::class);
        $mock->expects()->withdrawAmountAndCreateCardTransaction()->twice()->withAnyArgs(); // one for customer, second for guarantor
        $mock->expects()->withdrawAmountAndCreatePINFLTransaction()->times(0); // pinfl payment must not be run

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
            'card_after_due_days' => 0,
            'pinfl_after_due_days' => 0,
            'guarantor_after_due_days' => 1,
            'guarantor_pinfl_after_due_days' => 1,
            'any_card_charge' => true,
        ]);
        $customerCard = Card::factory()->create(['status' => CardStatusEnum::ACTIVE, 'customer_id' => $installment->customer_id]);
        $guarantor = Customer::factory()->create();
        $guarantorCard = Card::factory()->create(['status' => CardStatusEnum::ACTIVE, 'customer_id' => $guarantor->id]);
        $installment->guarantors()->attach($guarantor, ['any_card_charge' => true]);
        $installment->cards()->attach($customerCard);
        $installment->cards()->attach($guarantorCard);

        $job = new InstallmentChargeJob($installment->id);
        $mock = $this->mock(AtmosService::class);
        $mock->expects()->withdrawAmountAndCreateCardTransaction()->once()->withAnyArgs(); // one for customer
        $mock->expects()->withdrawAmountAndCreatePINFLTransaction()->once()->withAnyArgs(); // one for customer

        $job->handle($mock);
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
            'pinfl_after_due_days' => 2,
            'guarantor_after_due_days' => 2,
            'guarantor_pinfl_after_due_days' => 2,
            'any_card_charge' => true,
        ]);
        $card = Card::factory()->create(['status' => CardStatusEnum::ACTIVE, 'customer_id' => $installment->customer_id]);
        $installment->cards()->attach($card);

        $job = new InstallmentChargeJob($installment->id);
        $mock = $this->mock(AtmosService::class);
        $mock->expects()->withdrawAmountAndCreateCardTransaction()->once()->withAnyArgs();
        $mock->expects()->withdrawAmountAndCreatePINFLTransaction()->times(0);

        $job->handle($mock);
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
            'pinfl_after_due_days' => 1,
            'guarantor_after_due_days' => 1,
            'guarantor_pinfl_after_due_days' => 1,
            'any_card_charge' => true,
        ]);
        $cards = Card::factory()->count(2)->create(['status' => CardStatusEnum::ACTIVE, 'customer_id' => $installment->customer_id]);
        $installment->cards()->attach($cards);

        $job = new InstallmentChargeJob($installment->id);
        $mock = $this->mock(AtmosService::class);
        $mock->expects()->withdrawAmountAndCreateCardTransaction()->twice()->withAnyArgs();
        $mock->expects()->withdrawAmountAndCreatePINFLTransaction()->times(0);

        $job->handle($mock);
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
            'pinfl_after_due_days' => 1,
            'guarantor_after_due_days' => 1,
            'guarantor_pinfl_after_due_days' => 1,
            'any_card_charge' => true,
        ]);
        $card = Card::factory()->create(['status' => CardStatusEnum::ACTIVE, 'customer_id' => $installment->customer_id]);
        $installment->cards()->attach($card);
        $guarantor = Customer::factory()->create();
        $installment->guarantors()->attach($guarantor, ['any_card_charge' => true]);
        $guarantorCard = Card::factory()->create(['status' => CardStatusEnum::ACTIVE, 'customer_id' => $guarantor->id]);
        $installment->cards()->attach($guarantorCard);

        $job = new InstallmentChargeJob($installment->id);
        $mock = $this->mock(AtmosService::class);
        $mock->shouldReceive('withdrawAmountAndCreateCardTransaction')->once()->withArgs(fn ($card, $installment, $amount) => $amount === 100000.0);
        $mock->expects()->withdrawAmountAndCreatePINFLTransaction()->times(0);

        $job->handle($mock);
    }

    #[Test]
    public function triesToChargeWithExactAmountByPinfl(): void
    {
        $installment = Installment::factory()->create([
            'status' => InstallmentStatusEnum::ACTIVE,
            'starts_in' => now(),
            'amount' => 100000,
            'period' => 1,
            'percent' => 0,
            'initial_fee' => 0,
            'card_after_due_days' => 1,
            'pinfl_after_due_days' => 0,
            'guarantor_after_due_days' => 1,
            'guarantor_pinfl_after_due_days' => 1,
            'any_card_charge' => true,
        ]);
        $card = Card::factory()->create(['status' => CardStatusEnum::ACTIVE, 'customer_id' => $installment->customer_id]);
        $installment->cards()->attach($card);
        $guarantor = Customer::factory()->create();
        $installment->guarantors()->attach($guarantor, ['any_card_charge' => true]);
        $guarantorCard = Card::factory()->create(['status' => CardStatusEnum::ACTIVE, 'customer_id' => $guarantor->id]);
        $installment->cards()->attach($guarantorCard);

        $job = new InstallmentChargeJob($installment->id);
        $mock = $this->mock(AtmosService::class);
        $mock->expects()->withdrawAmountAndCreateCardTransaction()->times(0);
        $mock->shouldReceive('withdrawAmountAndCreatePINFLTransaction')->once()->withArgs(fn ($card, $installment, $amount) => $amount === 100000.0);

        $job->handle($mock);
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
            'pinfl_after_due_days' => 1,
            'guarantor_after_due_days' => 0,
            'guarantor_pinfl_after_due_days' => 1,
            'any_card_charge' => true,
        ]);
        $customerCard = Card::factory()->create(['status' => CardStatusEnum::ACTIVE, 'customer_id' => $installment->customer_id]);
        $installment->cards()->attach($customerCard);
        $guarantor = Customer::factory()->create();
        $guarantorCard = Card::factory()->create(['status' => CardStatusEnum::ACTIVE, 'customer_id' => $guarantor->id]);
        $installment->guarantors()->attach($guarantor, ['any_card_charge' => true]);
        $installment->cards()->attach($guarantorCard);

        $job = new InstallmentChargeJob($installment->id);
        $mock = $this->mock(AtmosService::class);
        $mock->shouldReceive('withdrawAmountAndCreateCardTransaction')->once()->withArgs(fn ($card, $installment, $amount) => $amount === 100000.0);
        $mock->expects()->withdrawAmountAndCreatePINFLTransaction()->times(0);

        $job->handle($mock);
    }

    #[Test]
    public function triesToChargeWithExactAmountByPinflFromGuarantor(): void
    {
        $installment = Installment::factory()->create([
            'status' => InstallmentStatusEnum::ACTIVE,
            'starts_in' => now(),
            'amount' => 100000,
            'period' => 1,
            'percent' => 0,
            'initial_fee' => 0,
            'card_after_due_days' => 1,
            'pinfl_after_due_days' => 1,
            'guarantor_after_due_days' => 1,
            'guarantor_pinfl_after_due_days' => 0,
            'any_card_charge' => true,
        ]);
        $customerCard = Card::factory()->create(['status' => CardStatusEnum::ACTIVE, 'customer_id' => $installment->customer_id]);
        $installment->cards()->attach($customerCard);
        $guarantor = Customer::factory()->create();
        $installment->guarantors()->attach($guarantor, ['any_card_charge' => true]);
        $guarantorCard = Card::factory()->create(['status' => CardStatusEnum::ACTIVE, 'customer_id' => $guarantor->id]);
        $installment->cards()->attach($guarantorCard);

        $job = new InstallmentChargeJob($installment->id);
        $mock = $this->mock(AtmosService::class);
        $mock->expects()->withdrawAmountAndCreateCardTransaction()->times(0);
        $mock->shouldReceive('withdrawAmountAndCreatePINFLTransaction')->once()->withArgs(fn ($card, $installment, $amount) => $amount === 100000.0);

        $job->handle($mock);
    }
}
