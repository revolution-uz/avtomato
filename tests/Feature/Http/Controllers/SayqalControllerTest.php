<?php

namespace Tests\Feature\Http\Controllers;

use App\Enums\Company\SmsMessageStatusEnum;
use App\Enums\FeatureEnum;
use App\Helpers\Utilities;
use App\Models\BaseFeature;
use App\Models\Company\Feature;
use App\Models\Company\SmsMessage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SayqalControllerTest extends TestCase
{
    protected bool $tenancy = true;

    private Feature $_feature;
    private SmsMessage $_message;
    private array $_input;
    private string $_token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->_message = SmsMessage::factory()->create();

        $baseFeature = BaseFeature::query()->where('name', FeatureEnum::SMS)->firstOrFail();
        $baseFeature->original_data = [
            'provider' => 'sms_sayqal',
            'username' => 'username',
            'secret' => 'secret'
        ];
        $baseFeature->save();

        $this->_feature = Feature::findByName(FeatureEnum::SMS);
        $this->_feature->amount = 65;
        $this->_feature->save();

        $this->_message->charge()->create([
            'feature_id' => $this->_feature->id,
            'amount' => $this->_feature->amount,
        ]);

        $this->_input = [
            'transactionid' => '6988790',
            'smsid' => tenant('id') . Utilities::TENANT_SEPARATOR . $this->_message->id,
            'status' => 0,
            'description' => 'DELIVERED',
            'statusdate' => '2019-10-17 12:01:03',
            'partinfo' => '1:1'
        ];

        $this->_token = md5("TransmitSMS {$baseFeature->original_data['username']} {$baseFeature->original_data['secret']} {$this->_input['statusdate']}");
    }

    #[Test]
    public function successSms()
    {
        $response = $this
            ->withHeader('X-Access-Token', $this->_token)
            ->postJson(
                route('sayqalCallback.sms'),
                $this->_input
            );

        $response->assertOk();
        $this->_message->refresh();
        $this->assertEquals(SmsMessageStatusEnum::DELIVERED, $this->_message->status);
    }

    #[Test]
    public function successWithLongSms()
    {
        $response = $this
            ->withHeader('X-Access-Token', $this->_token)
            ->postJson(
                route('sayqalCallback.sms'),
                array_merge($this->_input, ['partinfo' => '2:3'])
            );

        $response->assertOk();
        $this->_message->refresh();
        $this->assertEquals(SmsMessageStatusEnum::DELIVERED, $this->_message->status);
        $this->assertEquals($this->_feature->amount * 2, $this->_message->charge->amount);
    }

    #[Test]
    public function errorSms()
    {
        $response = $this
            ->withHeader('X-Access-Token', $this->_token)
            ->postJson(
                route('sayqalCallback.sms'),
                array_merge($this->_input, ['status' => 1, 'description' => 'UNDELIVERED'])
            );
        $response->assertStatus(501);
        $this->_message->refresh();
        $this->assertEquals(SmsMessageStatusEnum::UNDELIVERED, $this->_message->status);

        $response = $this
            ->withHeader('X-Access-Token', $this->_token)
            ->postJson(
                route('sayqalCallback.sms'),
                array_merge($this->_input, ['status' => 2, 'description' => 'EXPIRED'])
            );
        $response->assertStatus(501);
        $this->_message->refresh();
        $this->assertEquals(SmsMessageStatusEnum::EXPIRED, $this->_message->status);
    }

    #[Test]
    public function errorUnauthorized()
    {
        $response = $this
            ->postJson(
                route('sayqalCallback.sms'),
                $this->_input
            );
        $response->assertStatus(403);

        $response = $this
            ->withHeader('X-Access-Token', md5("TransmitSMS username-test secret-test {$this->_input['statusdate']}"))
            ->postJson(
                route('sayqalCallback.sms'),
                $this->_input
            );
        $response->assertStatus(403);
    }
}
