<?php

namespace Tests\Unit\Models;

use App\Models\Company\Card;
use App\Models\Company\Customer;
use App\Models\Company\Installment;
use App\Models\Company\Transaction;
use App\Models\Company\TransactionType;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TransactionModelTest extends TestCase
{
    protected bool $tenancy = true;

    private Installment $_installment;
    private TransactionType $_transactionType;
    private Customer $_customer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->_installment = Installment::factory()->create();
        $this->_transactionType = TransactionType::factory()->create();
        $this->_customer = Customer::factory()->create();
    }

    #[Test]
    public function successCreate(): void
    {
        $transaction = new Transaction;
        $transaction->installment_id = $this->_installment->id;
        $transaction->transaction_type_id = $this->_transactionType->id;
        $transaction->customer_id = $this->_customer->id;
        $transaction->card_id = $cardId = Card::factory()->create()->id;
        $transaction->admin_id = $this->createAdminUser()->id;
        $transaction->amount = 1000;
        $transaction->paid_at = $paidAt = Carbon::now()->subDays(rand(1, 100));

        $transaction->save();

        $this->assertEquals($this->_installment->id, $transaction->installment_id);
        $this->assertEquals($this->_transactionType->id, $transaction->transaction_type_id);
        $this->assertEquals($this->_customer->id, $transaction->customer_id);
        $this->assertEquals($this->createAdminUser()->id, $transaction->admin_id);
        $this->assertEquals($cardId, $transaction->card_id);
        $this->assertEquals(1000, $transaction->amount);
        $this->assertEquals($paidAt->setTime(0, 0), $transaction->paid_at);
        $this->assertNotNull($transaction->created_at);
        $this->assertNotNull($transaction->updated_at);
    }

    #[Test]
    public function setsPaidAtWhenNotGiven(): void
    {
        $transaction = new Transaction;
        $transaction->installment_id = $this->_installment->id;
        $transaction->transaction_type_id = $this->_transactionType->id;
        $transaction->customer_id = $this->_customer->id;
        $transaction->amount = 1000;
        $transaction->paid_at = null;

        $transaction->save();

        $this->assertNotNull($transaction->paid_at);
    }

    #[Test]
    public function setsAdminWhenNotGiven(): void
    {
        auth()->login($this->createAdminUser());
        $transaction = new Transaction;
        $transaction->installment_id = $this->_installment->id;
        $transaction->transaction_type_id = $this->_transactionType->id;
        $transaction->customer_id = $this->_customer->id;
        $transaction->amount = 1000;

        $transaction->save();

        $this->assertEquals($this->createAdminUser()->id, $transaction->admin_id);
    }

    #[Test]
    public function incrementsInstallmentsPaidAmount(): void
    {
        $oldPaidAmount = $this->_installment->paid_amount;
        $transaction = new Transaction;
        $transaction->installment_id = $this->_installment->id;
        $transaction->transaction_type_id = $this->_transactionType->id;
        $transaction->customer_id = $this->_customer->id;
        $transaction->amount = 1000;

        $transaction->save();

        $this->assertEquals($oldPaidAmount + 1000, $this->_installment->refresh()->paid_amount);
    }

    #[Test]
    public function decrementsInstallmentsPaidAmount()
    {
        $transaction = Transaction::factory()->create([
            'installment_id' => $this->_installment->id,
            'transaction_type_id' => $this->_transactionType->id,
            'customer_id' => $this->_customer->id,
            'amount' => 1000,
        ]);
        $this->_installment->refresh();
        $oldPaidAmount = $this->_installment->paid_amount;
        $transaction->delete();

        $this->assertEquals($oldPaidAmount - 1000, $this->_installment->refresh()->paid_amount);
    }
}
