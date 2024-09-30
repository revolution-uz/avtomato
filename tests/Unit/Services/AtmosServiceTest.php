<?php

namespace Tests\Unit\Services;

use App\Clients\AtmosCardClient;
use App\Clients\AtmosPinflClient;
use App\Enums\Company\TransactionAttemptStatusEnum;
use App\Enums\Company\TransactionAttemptTypeEnum;
use App\Enums\FeatureEnum;
use App\Models\Company\Card;
use App\Models\Company\Customer;
use App\Models\Company\Feature;
use App\Models\Company\Installment;
use App\Models\Company\Transaction;
use App\Models\Company\TransactionAttempt;
use App\Models\Company\TransactionType;
use App\Services\AtmosService;
use Database\Seeders\Company\TransactionTypeSeeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use PHPUnit\Framework\Attributes\Test;
use Tests\Fakers\AtmosApigwFaker;
use Tests\Fakers\AtmosFaker;
use Tests\TestCase;

class AtmosServiceTest extends TestCase
{
    protected bool $tenancy = true;

    private AtmosService $_atmosService;
    private Installment $_installment;
    private Card $_card;
    private Customer $_customer;
    private array $_featureData = [
        'username' => 'username',
        'password' => 'password',
        'store_id' => '1',
        'expires_in' => 3600,
        'access_token' => 'access_token',
        'token_type' => 'token_type',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        TransactionAttempt::query()->forceDelete();
        Transaction::query()->forceDelete();
        Installment::query()->forceDelete();

        $feature = Feature::findByName(FeatureEnum::ATMOS_CARD_PAY);
        $feature->data = $this->_featureData;
        $feature->saveOrFail();
        $feature = Feature::findByName(FeatureEnum::ATMOS_PINFL_PAY);
        $feature->data = $this->_featureData;
        $feature->saveOrFail();

        $this->_atmosService = new AtmosService(new AtmosCardClient, new AtmosPinflClient);
        Artisan::call('tenants:seed', ['--class' => TransactionTypeSeeder::class, '--tenants' => [tenant('id')]]);

        $this->_card = Card::factory()->create();
        $this->_customer = Customer::factory()->create();
        $this->_installment = Installment::factory()->create([
            'amount' => 1000000,
            'initial_fee' => 100000,
            'percent' => 15,
            'starts_in' => Carbon::now()->subDay(),
            'period' => 3,
            'paid_amount' => 0,
        ]);
    }

    #[Test]
    public function successWithdrawAmountAndCreateCardTransaction()
    {
        AtmosFaker::getToken();
        AtmosFaker::createPayment();
        AtmosFaker::preConfirmPayment();
        AtmosFaker::confirmPayment();

        $debtAmount = $this->_installment->debtAmount;

        $this->_atmosService->withdrawAmountAndCreateCardTransaction($this->_card, $this->_installment, $debtAmount);

        $transaction = Transaction::query()->first();
        $transactionAttempt = TransactionAttempt::query()->first();

        $this->assertEquals($this->_installment->id, $transactionAttempt->installment_id);
        $this->assertEquals($this->_card->customer_id, $transactionAttempt->customer_id);
        $this->assertEquals(TransactionAttemptStatusEnum::SUCCESS, $transactionAttempt->status);
        $this->assertEquals(345000, $transactionAttempt->amount);
        $this->assertEquals($this->_card->id, $transactionAttempt->card_id);
        $this->assertEquals(TransactionAttemptTypeEnum::ATMOS_PAY_WITH_CARD, $transactionAttempt->type);

        $this->assertEquals(TransactionType::ATMOS_WITH_CARD, $transaction->transaction_type_id);
        $this->assertEquals('13745301', $transaction->external_id);
        $this->assertEquals(2000, $transaction->amount);
        $this->assertEquals($this->_card->id, $transaction->card_id);
        $this->assertEquals($this->_installment->id, $transaction->installment_id);
        $this->assertEquals($this->_card->customer_id, $transaction->customer_id);
        $this->assertEquals($this->_card->pan, $transaction->card_pan);
        $this->assertEquals('2024-08-06 11:50:05', $transaction->paid_at->format('Y-m-d H:i:s'));

        $this->_installment->refresh();
        $this->assertEquals(343000, $debtAmount);
        $this->assertEquals(2000, $this->_installment->paid_amount);
        $this->assertEquals($this->_installment->monthlyPayment - 2000, $this->_installment->debtAmount);
    }

    #[Test]
    public function successWithdrawAmountAndCreatePINFLTransaction()
    {
        AtmosApigwFaker::getToken();
        AtmosApigwFaker::createPayment();
        AtmosApigwFaker::confirmPayment();

        $debtAmount = $this->_installment->debtAmount;

        $this->_atmosService->withdrawAmountAndCreatePINFLTransaction($this->_customer, $this->_installment, $debtAmount);

        $transactions = Transaction::query()->get();
        $transactionAttempts = TransactionAttempt::query()->get();

        $this->assertEquals(2, $transactions->count());
        $this->assertEquals(2, $transactionAttempts->count());

        $this->assertEquals($this->_installment->id, $transactionAttempts[0]->installment_id);
        $this->assertEquals($this->_customer->id, $transactionAttempts[0]->customer_id);
        $this->assertEquals(TransactionAttemptStatusEnum::SUCCESS, $transactionAttempts[0]->status);
        $this->assertEquals(345000, $transactionAttempts[0]->amount);
        $this->assertNull($transactionAttempts[0]->card_id);
        $this->assertEquals(TransactionAttemptTypeEnum::ATMOS_PAY_WITH_PINFL, $transactionAttempts[0]->type);

        $this->assertEquals($this->_installment->id, $transactionAttempts[1]->installment_id);
        $this->assertEquals($this->_customer->id, $transactionAttempts[1]->customer_id);
        $this->assertEquals(TransactionAttemptStatusEnum::SUCCESS, $transactionAttempts[1]->status);
        $this->assertEquals(344000, $transactionAttempts[1]->amount);
        $this->assertNull($transactionAttempts[1]->card_id);
        $this->assertEquals(TransactionAttemptTypeEnum::ATMOS_PAY_WITH_PINFL, $transactionAttempts[1]->type);

        $this->assertEquals(TransactionType::ATMOS_WITH_PINFL, $transactions[0]->transaction_type_id);
        $this->assertEquals('12345678', $transactions[0]->external_id);
        $this->assertEquals(1000, $transactions[0]->amount);
        $this->assertNull($transactions[0]->card_id);
        $this->assertEquals($this->_installment->id, $transactions[0]->installment_id);
        $this->assertEquals($this->_customer->id, $transactions[0]->customer_id);
        $this->assertEquals('860066******5544', $transactions[0]->card_pan);
        $this->assertTrue($transactions[0]->paid_at->between(Carbon::now()->subMinute(), Carbon::now()));

        $this->assertEquals(TransactionType::ATMOS_WITH_PINFL, $transactions[1]->transaction_type_id);
        $this->assertEquals('12345678', $transactions[1]->external_id);
        $this->assertEquals(1000, $transactions[1]->amount);
        $this->assertNull($transactions[1]->card_id);
        $this->assertEquals($this->_installment->id, $transactions[1]->installment_id);
        $this->assertEquals($this->_customer->id, $transactions[1]->customer_id);
        $this->assertEquals('860066******5555', $transactions[1]->card_pan);
        $this->assertTrue($transactions[1]->paid_at->between(Carbon::now()->subMinute(), Carbon::now()));

        $this->_installment->refresh();
        $this->assertEquals(343000, $debtAmount);
        $this->assertEquals(2000, $this->_installment->paid_amount);
        $this->assertEquals($this->_installment->monthlyPayment - 2000, $this->_installment->debtAmount);
    }

    #[Test]
    public function failWithdrawAmountAndCreateCardTransaction()
    {
        AtmosFaker::getToken();
        AtmosFaker::createPayment(true);

        $debtAmount = $this->_installment->debtAmount;

        $this->_atmosService->withdrawAmountAndCreateCardTransaction($this->_card, $this->_installment, $debtAmount);

        $transaction = Transaction::query()->first();
        $transactionAttempt = TransactionAttempt::query()->first();

        $this->assertNull($transaction);
        $this->assertNotNull($transactionAttempt);

        $this->assertEquals($this->_installment->id, $transactionAttempt->installment_id);
        $this->assertEquals($this->_card->customer_id, $transactionAttempt->customer_id);
        $this->assertEquals(TransactionAttemptStatusEnum::FAILED, $transactionAttempt->status);
        $this->assertEquals(345000, $transactionAttempt->amount);
        $this->assertEquals($this->_card->id, $transactionAttempt->card_id);
        $this->assertEquals(TransactionAttemptTypeEnum::ATMOS_PAY_WITH_CARD, $transactionAttempt->type);

        $this->_installment->refresh();
        $this->assertEquals($this->_installment->monthlyPayment, $debtAmount);
        $this->assertEquals(0, $this->_installment->paid_amount);
        $this->assertEquals($this->_installment->monthlyPayment, $this->_installment->debtAmount);
    }

    #[Test]
    public function failWithdrawAmountAndCreatePINFLTransaction()
    {
        AtmosApigwFaker::getToken();
        AtmosApigwFaker::createPayment(true);

        $debtAmount = $this->_installment->debtAmount;

        $this->_atmosService->withdrawAmountAndCreatePINFLTransaction($this->_customer, $this->_installment, $debtAmount);

        $transactions = Transaction::query()->get();
        $transactionAttempts = TransactionAttempt::query()->get();

        $this->assertEquals(0, $transactions->count());
        $this->assertEquals(1, $transactionAttempts->count());

        $this->assertEquals($this->_installment->id, $transactionAttempts[0]->installment_id);
        $this->assertEquals($this->_customer->id, $transactionAttempts[0]->customer_id);
        $this->assertEquals(TransactionAttemptStatusEnum::FAILED, $transactionAttempts[0]->status);
        $this->assertEquals(345000, $transactionAttempts[0]->amount);
        $this->assertNull($transactionAttempts[0]->card_id);
        $this->assertEquals(TransactionAttemptTypeEnum::ATMOS_PAY_WITH_PINFL, $transactionAttempts[0]->type);

        $this->_installment->refresh();
        $this->assertEquals(345000, $debtAmount);
        $this->assertEquals(0, $this->_installment->paid_amount);
        $this->assertEquals($this->_installment->monthlyPayment, $this->_installment->debtAmount);
    }
}
