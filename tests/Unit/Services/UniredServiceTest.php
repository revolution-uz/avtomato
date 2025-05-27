<?php

namespace Tests\Unit\Services;

use App\Clients\UniredPinflClient;
use App\DTOs\Unired\Payment\UniredGetPaymentsPaginationRequestDTO;
use App\Enums\Company\PhoneTypeEnum;
use App\Enums\FeatureEnum;
use App\Exceptions\UniredException;
use App\Models\CentralCustomer;
use App\Models\CentralUniredCard;
use App\Models\Company\Customer;
use App\Models\Company\Feature;
use App\Models\Company\Installment;
use App\Models\Company\Phone;
use App\Models\Company\Transaction;
use App\Services\UniredService;
use PHPUnit\Framework\Attributes\Test;
use Tests\Fakers\UniredFaker;
use Tests\TestCase;

class UniredServiceTest extends TestCase
{
    protected bool $tenancy = true;

    private UniredService $_uniredService;

    private array $_inputCompany = [
        'id' => 6,
        'name' => 'New Filial A',
        // 'address' => null,
        'merchant_uzcard' => '1234',
        'terminal_uzcard' => '1234',
        'merchant_humo' => '12345',
        'terminal_humo' => '12345',
        'phone' => '998931234567',
        // 'created_by' => 0,
        'auto_payment' => true,
    ];

    protected function setUp(): void
    {
        parent::setUp();

        CentralCustomer::truncate();
        CentralUniredCard::truncate();

        $this->_uniredService = new UniredService(new UniredPinflClient);
    }

    #[Test]
    public function successCreateBranch()
    {
        UniredFaker::createBranch();

        $response = $this->_uniredService->createBranch(UniredCreateBranchRequestDTO::from($this->_inputCompany));

        $this->assertTrue($response);
        $feature = Feature::findByName(FeatureEnum::UNIRED_PAY);
        $this->assertEquals($this->_inputCompany['id'], $feature->data['id']);
        $this->assertEquals($this->_inputCompany['name'], $feature->data['name']);
        $this->assertEquals($this->_inputCompany['merchant_uzcard'], $feature->data['merchant_uzcard']);
        $this->assertEquals($this->_inputCompany['terminal_uzcard'], $feature->data['terminal_uzcard']);
        $this->assertEquals($this->_inputCompany['merchant_humo'], $feature->data['merchant_humo']);
        $this->assertEquals($this->_inputCompany['terminal_humo'], $feature->data['terminal_humo']);
        $this->assertEquals($this->_inputCompany['phone'], $feature->data['phone']);
        $this->assertFalse(isset($feature->data['auto_payment']));
    }

    #[Test]
    public function successUpdateBranch()
    {
        UniredFaker::updateBranch();
        $feature = Feature::findByName(FeatureEnum::UNIRED_PAY);
        $feature->data = array_merge($this->_inputCompany, ['name' => 'Before Updated']);
        $feature->save();

        $response = $this->_uniredService->updateBranch(UniredUpdateBranchRequestDTO::from($this->_inputCompany));

        $this->assertTrue($response);
        $feature->refresh();
        $this->assertEquals($this->_inputCompany['id'], $feature->data['id']);
        $this->assertEquals($this->_inputCompany['name'], $feature->data['name']);
        $this->assertEquals($this->_inputCompany['merchant_uzcard'], $feature->data['merchant_uzcard']);
        $this->assertEquals($this->_inputCompany['terminal_uzcard'], $feature->data['terminal_uzcard']);
        $this->assertEquals($this->_inputCompany['merchant_humo'], $feature->data['merchant_humo']);
        $this->assertEquals($this->_inputCompany['terminal_humo'], $feature->data['terminal_humo']);
        $this->assertEquals($this->_inputCompany['phone'], $feature->data['phone']);
        $this->assertEquals($this->_inputCompany['auto_payment'], $feature->data['auto_payment']);
    }

    #[Test]
    public function successSwitchOffCompany()
    {
        UniredFaker::switchOffBranch();
        $feature = Feature::findByName(FeatureEnum::UNIRED_PAY);
        $feature->data = $this->_inputCompany;
        $feature->save();

        $this->_uniredService->switchOffBranch();

        $feature->refresh();
        $this->assertEquals($this->_inputCompany['id'], $feature->data['id']);
        $this->assertEquals($this->_inputCompany['name'], $feature->data['name']);
        $this->assertEquals($this->_inputCompany['merchant_uzcard'], $feature->data['merchant_uzcard']);
        $this->assertEquals($this->_inputCompany['terminal_uzcard'], $feature->data['terminal_uzcard']);
        $this->assertEquals($this->_inputCompany['merchant_humo'], $feature->data['merchant_humo']);
        $this->assertEquals($this->_inputCompany['terminal_humo'], $feature->data['terminal_humo']);
        $this->assertEquals($this->_inputCompany['phone'], $feature->data['phone']);
        $this->assertFalse($feature->data['auto_payment']);
    }

    #[Test]
    public function successCreateOrUpdateCustomer()
    {
        $feature = Feature::findByName(FeatureEnum::UNIRED_PINFL_PAY);
        $feature->data = FeatureEnum::UNIRED_PINFL_PAY->getExampleData();
        $feature->saveOrFail();

        UniredFaker::createOrUpdateCustomer();
        $installment = Installment::factory()->create();
        $phones = Phone::factory()->count(5)->create();
        $installment->customer->phones()->attach($phones, ['type' => PhoneTypeEnum::PRIVATE]);

        $response = $this->_uniredService->updateInstallment($installment);

        $this->assertTrue($response);
    }

    #[Test]
    public function successGetCards()
    {
        UniredFaker::getCards();

        $customer = Customer::factory()->create();
        $centralCustomer = $customer->getOrCreateCentralCustomer();
        $expectedData = [
            [
                'pinfl' => '413109***0086',
                'uuid' => 'KB0RWS53CAQDVHFZ9J11T560Y',
                'auto' => true,
                'is_verified' => true,
                'is_blocked' => false,
                'block_reason' => null,
                'blocked_at' => null,
                'owner' => 'John Doe',
                'pan' => '860006******6735',
                'phone' => '',
                'type' => 1,
            ],
            [
                'pinfl' => '413109***0086',
                'uuid' => '3Y1JYBWQQDY1V0T93WCS6HF1',
                'auto' => true,
                'is_verified' => true,
                'is_blocked' => true,
                'block_reason' => 'Card not found',
                'blocked_at' => '2025-01-17T08:32:44+05:00',
                'owner' => 'John Doe',
                'pan' => '986060******8244',
                'phone' => '9989939**000',
                'type' => 2,
            ],
        ];

        $res = $this->_uniredService->getAndSyncPinflCards($customer);

        $this->assertTrue($res);
        $this->assertCount(count($expectedData), $centralCustomer->uniredCards);

        foreach ($centralCustomer->uniredCards as $key => $card) {
            $this->assertEquals($expectedData[$key]['pinfl'], $card->pinfl);
            $this->assertEquals($expectedData[$key]['uuid'], $card->uuid);
            $this->assertEquals($expectedData[$key]['auto'], $card->auto);
            $this->assertEquals($expectedData[$key]['is_verified'], $card->is_verified);
            $this->assertEquals($expectedData[$key]['is_blocked'], $card->is_blocked);
            $this->assertEquals($expectedData[$key]['block_reason'], $card->block_reason);
            $this->assertEquals($expectedData[$key]['blocked_at'], $card->blocked_at?->format('Y-m-d\TH:i:sP'));
            $this->assertEquals($expectedData[$key]['owner'], $card->owner);
            $this->assertEquals($expectedData[$key]['pan'], $card->pan);
            $this->assertEquals($expectedData[$key]['phone'], $card->phone);
            $this->assertEquals($expectedData[$key]['type'], $card->type?->value);
        }
    }

    #[Test]
    public function successGetPayments()
    {
        UniredFaker::getPayments();
        $response = $this->_uniredService->getPayments(new UniredGetPaymentsPaginationRequestDTO(1, 1, '2010-10-10', '2010-10-10'));
        $this->assertNotNull($response);
    }

    #[Test]
    public function successReversePayment()
    {
        UniredFaker::reversePayment();
        $transaction = Transaction::factory()->create();
        $response = $this->_uniredService->reversePayment($transaction->external_id);
        $this->assertNotNull($response);
    }

    #[Test]
    public function successSetWebhook()
    {
        UniredFaker::setWebhook();
        $response = $this->_uniredService->setWebhook('https://example.com/api/v1', true);
        $this->assertNotNull($response);
    }

    #[Test]
    public function checkingForBadResponse()
    {
        UniredFaker::badResponse();
        $this->expectException(UniredException::class);
        $this->expectExceptionMessage('Error message!');
        $this->_uniredService->setWebhook('https://example.com/api/v1', true);
    }

    #[Test]
    public function checkingForEmptyResponse()
    {
        UniredFaker::emptyResponse();
        $this->expectException(UniredException::class);
        $this->expectExceptionMessage('Invalid response');
        $this->_uniredService->setWebhook('https://example.com/api/v1', true);
    }
}
