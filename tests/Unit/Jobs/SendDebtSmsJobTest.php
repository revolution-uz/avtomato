<?php

namespace Tests\Unit\Jobs;

use App\Enums\Company\CompanySettingsEnum;
use App\Enums\Company\PhoneTypeEnum;
use App\Enums\Company\SmsMessageStatusEnum;
use App\Enums\FeatureEnum;
use App\Enums\SmsTemplateEnum;
use App\Jobs\SendDebtSmsJob;
use App\Models\Company\CompanySettings;
use App\Models\Company\Customer;
use App\Models\Company\Feature;
use App\Models\Company\Installment;
use App\Models\Company\Phone;
use App\Models\Company\SmsMessage;
use App\Models\Company\SmsTemplate;
use App\Services\SmsService;
use App\Services\User\BalanceService;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SendDebtSmsJobTest extends TestCase
{
    protected bool $tenancy = true;

    private Customer $_customer;
    private Phone $_phone;
    private Installment $_installment;

    protected function setUp(): void
    {
        parent::setUp();

        CompanySettings::query()->where('name', CompanySettingsEnum::SMS_INSTALLMENT_PAYMENT)->update(['value' => 1]);

        $this->_customer = Customer::factory()->create();
        $this->_phone = Phone::factory()->create();
        $this->_customer->phones()->attach($this->_phone->id, ['type' => PhoneTypeEnum::randomValue(), 'installment_notifications' => true]);
        $this->_installment = Installment::factory()->create(['customer_id' => $this->_customer->id, 'starts_in' => Carbon::now()]);
    }

    #[Test]
    public function success(): void
    {
        $smsServiceMock = $this->mock(SmsService::class);
        $smsServiceMock->shouldReceive('sendSms')->once();
        $balanceServiceMock = $this->mock(BalanceService::class);
        $balanceServiceMock->shouldReceive('isBalanceEnough')->once()->andReturn(true);
        $balanceServiceMock->shouldReceive('chargeAmount')->once();

        (new SendDebtSmsJob($this->_installment->id))->handle($smsServiceMock, $balanceServiceMock);

        $smsMessage = SmsMessage::query()->firstOrFail();
        $this->assertEquals($this->_phone->phone, $smsMessage->phone);
        $this->assertEquals(
            SmsTemplate::query()->findOrFail(SmsTemplateEnum::DEBT->value)->getMessage([
                'credit_number' => $this->_installment->id,
                'company' => tenant('name'),
                'amount' => $this->_installment->debtAmount,
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
        Feature::findByName(FeatureEnum::SMS)->update(['is_active' => false]);

        $smsServiceMock = $this->mock(SmsService::class);
        $smsServiceMock->shouldReceive('sendSms')->times(0);
        $balanceServiceMock = $this->mock(BalanceService::class);
        $balanceServiceMock->shouldReceive('isBalanceEnough')->times(0);
        $balanceServiceMock->shouldReceive('chargeAmount')->times(0);

        (new SendDebtSmsJob($this->_installment->id))->handle($smsServiceMock, $balanceServiceMock);

        $this->assertEquals(0, SmsMessage::query()->count());
    }

    #[Test]
    public function failWithInsufficientBalance(): void
    {
        $smsServiceMock = $this->mock(SmsService::class);
        $smsServiceMock->shouldReceive('sendSms')->times(0);
        $balanceServiceMock = $this->mock(BalanceService::class);
        $balanceServiceMock->shouldReceive('isBalanceEnough')->once()->andReturn(false);
        $balanceServiceMock->shouldReceive('chargeAmount')->times(0);

        (new SendDebtSmsJob($this->_installment->id))->handle($smsServiceMock, $balanceServiceMock);

        $this->assertEquals(0, SmsMessage::query()->count());
    }

    #[Test]
    public function failWithTurnedOffSmsInstallmentPayment(): void
    {
        CompanySettings::query()->where('name', CompanySettingsEnum::SMS_INSTALLMENT_PAYMENT)->update(['value' => 0]);

        $smsServiceMock = $this->mock(SmsService::class);
        $smsServiceMock->shouldReceive('sendSms')->times(0);
        $balanceServiceMock = $this->mock(BalanceService::class);
        $balanceServiceMock->shouldReceive('isBalanceEnough')->once()->andReturn(false);
        $balanceServiceMock->shouldReceive('chargeAmount')->times(0);

        (new SendDebtSmsJob($this->_installment->id))->handle($smsServiceMock, $balanceServiceMock);

        $this->assertEquals(0, SmsMessage::query()->count());
    }

    #[Test]
    public function failWithTurnedOffPhoneInstallmentNotifications(): void
    {
        $this->_customer = Customer::factory()->create();
        $this->_phone = Phone::factory()->create();
        $this->_customer->phones()->attach($this->_phone->id, ['type' => PhoneTypeEnum::randomValue(), 'installment_notifications' => false]);
        $this->_installment = Installment::factory()->create(['customer_id' => $this->_customer->id]);

        $smsServiceMock = $this->mock(SmsService::class);
        $smsServiceMock->shouldReceive('sendSms')->times(0);
        $balanceServiceMock = $this->mock(BalanceService::class);
        $balanceServiceMock->shouldReceive('isBalanceEnough')->once()->andReturn(true);
        $balanceServiceMock->shouldReceive('chargeAmount')->times(0);

        (new SendDebtSmsJob($this->_installment->id))->handle($smsServiceMock, $balanceServiceMock);

        $this->assertEquals(0, SmsMessage::query()->count());
    }
}
