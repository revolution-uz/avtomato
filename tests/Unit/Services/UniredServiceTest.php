<?php

namespace Tests\Unit\Services;

use App\Clients\UniredClient;
use App\DTOs\Unired\Branch\UniredCreateBranchRequestDTO;
use App\DTOs\Unired\Branch\UniredUpdateBranchRequestDTO;
use App\DTOs\Unired\Payment\UniredGetPaymentsPaginationRequestDTO;
use App\Enums\Company\PhoneTypeEnum;
use App\Enums\FeatureEnum;
use App\Exceptions\UniredException;
use App\Models\Company\Customer;
use App\Models\Company\Feature;
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

        $this->_uniredService = new UniredService(new UniredClient);
    }

    #[Test]
    public function successCreateBranch()
    {
        UniredFaker::createBranch();

        $response = $this->_uniredService->createCompany(UniredCreateBranchRequestDTO::from($this->_inputCompany));

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

        $response = $this->_uniredService->updateCompany(UniredUpdateBranchRequestDTO::from($this->_inputCompany));

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
        UniredFaker::switchOffCompany();
        $feature = Feature::findByName(FeatureEnum::UNIRED_PAY);
        $feature->data = $this->_inputCompany;
        $feature->save();

        $this->_uniredService->switchOffCompany();

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
        UniredFaker::createOrUpdateCustomer();
        $customer = Customer::factory()->create();
        $phones = Phone::factory()->count(5)->create();
        $customer->phones()->attach($phones, ['type' => PhoneTypeEnum::PRIVATE]);

        $response = $this->_uniredService->createOrUpdateCustomer($customer);

        $this->assertTrue($response);
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
        $response = $this->_uniredService->reversePayment($transaction);
        $this->assertNotNull($response);
    }

    #[Test]
    public function successSetWebhook()
    {
        UniredFaker::setWebhook();
        $response = $this->_uniredService->setWebhook();
        $this->assertNotNull($response);
    }

    #[Test]
    public function checkingForBadResponse()
    {
        UniredFaker::badResponse();
        $this->expectException(UniredException::class);
        $this->expectExceptionMessage('Error message!');
        $this->_uniredService->setWebhook();
    }

    #[Test]
    public function checkingForEmptyResponse()
    {
        UniredFaker::emptyResponse();
        $this->expectException(UniredException::class);
        $this->expectExceptionMessage('Invalid response');
        $this->_uniredService->setWebhook();
    }
}
