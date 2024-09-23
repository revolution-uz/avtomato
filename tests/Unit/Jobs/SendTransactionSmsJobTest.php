<?php

namespace Tests\Unit\Jobs;

use App\Enums\Company\CompanySettingsEnum;
use App\Enums\Company\PhoneTypeEnum;
use App\Enums\Company\SmsMessageStatusEnum;
use App\Enums\FeatureEnum;
use App\Enums\SmsTemplateEnum;
use App\Jobs\SendTransactionSmsJob;
use App\Models\Company\CompanySettings;
use App\Models\Company\Customer;
use App\Models\Company\Feature;
use App\Models\Company\Phone;
use App\Models\Company\SmsMessage;
use App\Models\Company\SmsTemplate;
use App\Models\Company\Transaction;
use App\Models\Company\TransactionType;
use App\Services\SmsService;
use App\Services\User\BalanceService;
use Database\Seeders\Company\CompanySettingsSeeder;
use Database\Seeders\Company\TransactionTypeSeeder;
use Illuminate\Support\Arr;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SendTransactionSmsJobTest extends TestCase
{
    protected bool $tenancy = true;

    private Customer $_customer;
    private Phone $_phone;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('db:seed', ['--class' => TransactionTypeSeeder::class]);
        $this->artisan('db:seed', ['--class' => CompanySettingsSeeder::class]);

        $this->_customer = Customer::factory()->create();
        $this->_phone = Phone::factory()->create();
        $this->_customer->phones()->attach($this->_phone->id, ['type' => PhoneTypeEnum::randomValue(), 'installment_notifications' => true]);
    }

    #[Test]
    public function successWithAutoCharge(): void
    {
        $transactionType = TransactionType::query()->findOrFail(Arr::random(TransactionType::autoPayList()));

        /** @var Transaction $transaction */
        $transaction = Transaction::factory()->make(['transaction_type_id' => $transactionType->id, 'customer_id' => $this->_customer->id]);
        $transaction->saveQuietly();

        $smsServiceMock = $this->mock(SmsService::class);
        $smsServiceMock->shouldReceive('sendSms')->once();
        Mockery::mock(BalanceService::class)->makePartial();
        $balanceServiceMock = $this->mock(BalanceService::class);
        $balanceServiceMock->shouldReceive('isBalanceEnough')->once()->andReturn(true);
        $balanceServiceMock->shouldReceive('chargeAmount')->once();

        (new SendTransactionSmsJob($transaction->id))->handle($smsServiceMock, $balanceServiceMock);

        $smsMessage = SmsMessage::query()->firstOrFail();
        $this->assertEquals($this->_phone->phone, $smsMessage->phone);
        $this->assertEquals(
            SmsTemplate::query()->findOrFail(SmsTemplateEnum::AUTO_CHARGE->value)->getMessage([
                'pan' => $transaction->card_pan,
                'amount' => $transaction->amount,
                'credit_number' => $transaction->installment_id,
                'company' => tenant('name'),
                'phone' => tenant('owner_phone'),
            ]),
            $smsMessage->message
        );
        $this->assertEquals(SmsMessageStatusEnum::ACCEPTED, $smsMessage->status);
        $this->assertNull($smsMessage->ext_id);
    }

    #[Test]
    public function successWithManualCharge(): void
    {
        $setting = CompanySettings::query()->where('name', CompanySettingsEnum::SMS_INSTALLMENT_NOTIFICATION)->firstOrFail();
        $setting->value = '1';
        $setting->save();

        /** @var Transaction $transaction */
        $transaction = Transaction::factory()->make(['customer_id' => $this->_customer->id]);
        $transaction->saveQuietly();

        $smsServiceMock = $this->mock(SmsService::class);
        $smsServiceMock->shouldReceive('sendSms')->once();
        $balanceServiceMock = $this->mock(BalanceService::class);
        $balanceServiceMock->shouldReceive('isBalanceEnough')->once()->andReturn(true);
        $balanceServiceMock->shouldReceive('chargeAmount')->once();

        (new SendTransactionSmsJob($transaction->id))->handle($smsServiceMock, $balanceServiceMock);

        $smsMessage = SmsMessage::query()->firstOrFail();
        $this->assertEquals($this->_phone->phone, $smsMessage->phone);
        $this->assertEquals(
            SmsTemplate::query()->findOrFail(SmsTemplateEnum::CHARGE->value)->getMessage([
                'amount' => $transaction->amount,
                'credit_number' => $transaction->installment_id,
                'company' => tenant('name'),
                'phone' => tenant('owner_phone'),
            ]),
            $smsMessage->message
        );
        $this->assertEquals(SmsMessageStatusEnum::ACCEPTED, $smsMessage->status);
        $this->assertNull($smsMessage->ext_id);
    }

    #[Test]
    public function failWithInactiveFeature(): void
    {
        $feature = Feature::findByName(FeatureEnum::SMS);
        $feature->is_active = false;
        $feature->save();

        /** @var Transaction $transaction */
        $transaction = Transaction::factory()->make(['customer_id' => $this->_customer->id]);
        $transaction->saveQuietly();

        $smsServiceMock = $this->mock(SmsService::class);
        $smsServiceMock->shouldReceive('sendSms')->times(0);
        $balanceServiceMock = $this->mock(BalanceService::class);
        $balanceServiceMock->shouldReceive('isBalanceEnough')->times(0);
        $balanceServiceMock->shouldReceive('chargeAmount')->times(0);

        (new SendTransactionSmsJob($transaction->id))->handle($smsServiceMock, $balanceServiceMock);

        $this->assertEquals(0, SmsMessage::query()->count());
    }

    #[Test]
    public function failWithInsufficientBalance(): void
    {
        /** @var Transaction $transaction */
        $transaction = Transaction::factory()->make(['customer_id' => $this->_customer->id]);
        $transaction->saveQuietly();

        $smsServiceMock = $this->mock(SmsService::class);
        $smsServiceMock->shouldReceive('sendSms')->times(0);
        $balanceServiceMock = $this->mock(BalanceService::class);
        $balanceServiceMock->shouldReceive('isBalanceEnough')->once()->andReturn(false);
        $balanceServiceMock->shouldReceive('chargeAmount')->times(0);

        (new SendTransactionSmsJob($transaction->id))->handle($smsServiceMock, $balanceServiceMock);

        $this->assertEquals(0, SmsMessage::query()->count());
    }

    #[Test]
    public function failWithTurnedOffAutoChargeSetting(): void
    {
        $setting = CompanySettings::query()->where('name', CompanySettingsEnum::SMS_INSTALLMENT_AUTO_CHARGE)->firstOrFail();
        $setting->value = '0';
        $setting->save();

        $transactionType = TransactionType::query()->findOrFail(Arr::random(TransactionType::autoPayList()));

        /** @var Transaction $transaction */
        $transaction = Transaction::factory()->make(['transaction_type_id' => $transactionType->id, 'customer_id' => $this->_customer->id]);
        $transaction->saveQuietly();

        $smsServiceMock = $this->mock(SmsService::class);
        $smsServiceMock->shouldReceive('sendSms')->times(0);
        $balanceServiceMock = $this->mock(BalanceService::class);
        $balanceServiceMock->shouldReceive('isBalanceEnough')->once()->andReturn(true);
        $balanceServiceMock->shouldReceive('chargeAmount')->times(0);

        (new SendTransactionSmsJob($transaction->id))->handle($smsServiceMock, $balanceServiceMock);

        $this->assertEquals(0, SmsMessage::query()->count());
    }

    #[Test]
    public function failWithTurnedOffManualChargeSetting(): void
    {
        $setting = CompanySettings::query()->where('name', CompanySettingsEnum::SMS_INSTALLMENT_NOTIFICATION)->firstOrFail();
        $setting->value = '0';
        $setting->save();

        /** @var Transaction $transaction */
        $transaction = Transaction::factory()->make(['customer_id' => $this->_customer->id]);
        $transaction->saveQuietly();

        $smsServiceMock = $this->mock(SmsService::class);
        $smsServiceMock->shouldReceive('sendSms')->times(0);
        $balanceServiceMock = $this->mock(BalanceService::class);
        $balanceServiceMock->shouldReceive('isBalanceEnough')->once()->andReturn(true);
        $balanceServiceMock->shouldReceive('chargeAmount')->times(0);

        (new SendTransactionSmsJob($transaction->id))->handle($smsServiceMock, $balanceServiceMock);

        $this->assertEquals(0, SmsMessage::query()->count());
    }

    #[Test]
    public function failWithTurnedOffPhoneInstallmentNotifications(): void
    {
        $this->_customer = Customer::factory()->create();
        $this->_phone = Phone::factory()->create();
        $this->_customer->phones()->attach($this->_phone->id, ['type' => PhoneTypeEnum::randomValue(), 'installment_notifications' => false]);

        $transactionType = TransactionType::query()->findOrFail(Arr::random(TransactionType::autoPayList()));

        /** @var Transaction $transaction */
        $transaction = Transaction::factory()->make(['transaction_type_id' => $transactionType->id, 'customer_id' => $this->_customer->id]);
        $transaction->saveQuietly();

        $smsServiceMock = $this->mock(SmsService::class);
        $smsServiceMock->shouldReceive('sendSms')->times(0);
        $balanceServiceMock = $this->mock(BalanceService::class);
        $balanceServiceMock->shouldReceive('isBalanceEnough')->once()->andReturn(true);
        $balanceServiceMock->shouldReceive('chargeAmount')->times(0);

        (new SendTransactionSmsJob($transaction->id))->handle($smsServiceMock, $balanceServiceMock);

        $this->assertEquals(0, SmsMessage::query()->count());
    }
}
