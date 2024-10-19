<?php

namespace Tests\Unit\Jobs;

use App\Enums\Company\CompanySettingsEnum;
use App\Enums\Company\InstallmentStatusEnum;
use App\Enums\Company\PhoneTypeEnum;
use App\Enums\Company\SmsMessageStatusEnum;
use App\Enums\Company\SmsTemplateEnum;
use App\Enums\Company\SmsTemplateStatusEnum;
use App\Enums\FeatureEnum;
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
    private $_smsServiceMock;
    private $_balanceServiceMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->_smsServiceMock = $this->mock(SmsService::class);
        $this->_balanceServiceMock = $this->mock(BalanceService::class);
        CompanySettings::query()->where('name', CompanySettingsEnum::SMS_INSTALLMENT_PAYMENT)->update(['value' => 1]);

        $this->_customer = Customer::factory()->create();
        $this->_phone = Phone::factory()->create();
        $this->_customer->phones()->attach($this->_phone->id, ['type' => PhoneTypeEnum::randomValue(), 'installment_notifications' => true]);
        $this->_installment = Installment::factory()->create(['customer_id' => $this->_customer->id]);

        SmsTemplate::factory()->create([
            'status' => SmsTemplateStatusEnum::ACTIVE,
            'template' => 'One day left to pay!',
            'data' => [
                'category' => 'debt-templates',
                'overdue_days' => -1,
            ],
        ]);
        SmsTemplate::factory()->create([
            'status' => SmsTemplateStatusEnum::ACTIVE,
            'template' => 'Payment is one day late!',
            'data' => [
                'category' => 'debt-templates',
                'overdue_days' => 1,
            ],
        ]);
    }

    #[Test]
    public function successSendDebtSmsForToday(): void
    {
        $this->_smsServiceMock->shouldReceive('sendSms')->once();
        $this->_balanceServiceMock->shouldReceive('isBalanceEnough')->once()->andReturn(true);
        $this->_balanceServiceMock->shouldReceive('chargeAmount')->once();

        $this->_installment->update(['starts_in' => Carbon::today()]);

        (new SendDebtSmsJob($this->_installment->id))->handle($this->_smsServiceMock, $this->_balanceServiceMock);

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
    public function successSendDebtSmsBeforeOneDay(): void
    {
        $this->_smsServiceMock->shouldReceive('sendSms')->once();
        $this->_balanceServiceMock->shouldReceive('isBalanceEnough')->once()->andReturn(true);
        $this->_balanceServiceMock->shouldReceive('chargeAmount')->once();

        $this->_installment->update(['starts_in' => Carbon::today()->addDay()]);

        (new SendDebtSmsJob($this->_installment->id))->handle($this->_smsServiceMock, $this->_balanceServiceMock);

        $smsMessage = SmsMessage::query()->firstOrFail();
        $this->assertEquals($this->_phone->phone, $smsMessage->phone);
        $this->assertEquals('One day left to pay!', $smsMessage->message);
        $this->assertEquals(SmsMessageStatusEnum::ACCEPTED, $smsMessage->status);
        $this->assertNull($smsMessage->ext_id);
    }

    #[Test]
    public function successSendDebtSmsAfterOneDay(): void
    {
        $this->_smsServiceMock->shouldReceive('sendSms')->once();
        $this->_balanceServiceMock->shouldReceive('isBalanceEnough')->once()->andReturn(true);
        $this->_balanceServiceMock->shouldReceive('chargeAmount')->once();

        $this->_installment->update(['starts_in' => Carbon::today()->subDay()]);

        (new SendDebtSmsJob($this->_installment->id))->handle($this->_smsServiceMock, $this->_balanceServiceMock);

        $smsMessage = SmsMessage::query()->firstOrFail();
        $this->assertEquals($this->_phone->phone, $smsMessage->phone);
        $this->assertEquals('Payment is one day late!', $smsMessage->message);
        $this->assertEquals(SmsMessageStatusEnum::ACCEPTED, $smsMessage->status);
        $this->assertNull($smsMessage->ext_id);
    }

    #[Test]
    public function doNotSendDebtSmsIfThereIsNoAvailable(): void
    {
        $this->_smsServiceMock->shouldReceive('sendSms')->times(0);
        $this->_balanceServiceMock->shouldReceive('isBalanceEnough')->times(3)->andReturn(true);
        $this->_balanceServiceMock->shouldReceive('chargeAmount')->times(0);

        SmsTemplate::debtTemplates()->update(['data->is_active' => false]);
        $installments = [];
        $installments[] = Installment::factory()->create(['status' => InstallmentStatusEnum::ACTIVE, 'starts_in' => Carbon::today()->subDay()]);
        $installments[] = Installment::factory()->create(['status' => InstallmentStatusEnum::ACTIVE, 'starts_in' => Carbon::today()]);
        $installments[] = Installment::factory()->create(['status' => InstallmentStatusEnum::ACTIVE, 'starts_in' => Carbon::today()->addDay()]);

        foreach ($installments as $installment) {
            (new SendDebtSmsJob($installment->id))->handle($this->_smsServiceMock, $this->_balanceServiceMock);
        }

        $this->assertEquals(0, SmsMessage::query()->count());
    }

    #[Test]
    public function failWithInactiveFeature(): void
    {
        Feature::findByName(FeatureEnum::SMS)->update(['is_active' => false]);

        $this->_smsServiceMock->shouldReceive('sendSms')->times(0);
        $this->_balanceServiceMock->shouldReceive('isBalanceEnough')->times(0);
        $this->_balanceServiceMock->shouldReceive('chargeAmount')->times(0);

        (new SendDebtSmsJob($this->_installment->id))->handle($this->_smsServiceMock, $this->_balanceServiceMock);

        $this->assertEquals(0, SmsMessage::query()->count());
    }

    #[Test]
    public function failWithInsufficientBalance(): void
    {
        $this->_smsServiceMock->shouldReceive('sendSms')->times(0);
        $this->_balanceServiceMock->shouldReceive('isBalanceEnough')->once()->andReturn(false);
        $this->_balanceServiceMock->shouldReceive('chargeAmount')->times(0);

        (new SendDebtSmsJob($this->_installment->id))->handle($this->_smsServiceMock, $this->_balanceServiceMock);

        $this->assertEquals(0, SmsMessage::query()->count());
    }

    #[Test]
    public function failWithTurnedOffSmsInstallmentPayment(): void
    {
        CompanySettings::query()->where('name', CompanySettingsEnum::SMS_INSTALLMENT_PAYMENT)->update(['value' => 0]);

        $this->_smsServiceMock->shouldReceive('sendSms')->times(0);
        $this->_balanceServiceMock->shouldReceive('isBalanceEnough')->times(0);
        $this->_balanceServiceMock->shouldReceive('chargeAmount')->times(0);

        (new SendDebtSmsJob($this->_installment->id))->handle($this->_smsServiceMock, $this->_balanceServiceMock);

        $this->assertEquals(0, SmsMessage::query()->count());
    }

    #[Test]
    public function failWithTurnedOffPhoneInstallmentNotifications(): void
    {
        $this->_customer = Customer::factory()->create();
        $this->_phone = Phone::factory()->create();
        $this->_customer->phones()->attach($this->_phone->id, ['type' => PhoneTypeEnum::randomValue(), 'installment_notifications' => false]);
        $this->_installment = Installment::factory()->create(['customer_id' => $this->_customer->id, 'starts_in' => Carbon::now()]);

        $this->_smsServiceMock->shouldReceive('sendSms')->times(0);
        $this->_balanceServiceMock->shouldReceive('isBalanceEnough')->once()->andReturn(true);
        $this->_balanceServiceMock->shouldReceive('chargeAmount')->times(0);

        (new SendDebtSmsJob($this->_installment->id))->handle($this->_smsServiceMock, $this->_balanceServiceMock);

        $this->assertEquals(0, SmsMessage::query()->count());
    }
}
