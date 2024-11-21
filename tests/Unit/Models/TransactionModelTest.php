<?php

namespace Tests\Unit\Models;

use App\Enums\Company\InstallmentStatusEnum;
use App\Enums\Company\TransactionStatusEnum;
use App\Helpers\InstallmentGraphHelper;
use App\Models\Company\Card;
use App\Models\Company\CompanyAdmin;
use App\Models\Company\Customer;
use App\Models\Company\Installment;
use App\Models\Company\Transaction;
use App\Models\Company\TransactionType;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Bus;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TransactionModelTest extends TestCase
{
    protected bool $tenancy = true;

    private Installment $_installment;
    private TransactionType $_transactionType;
    private Customer $_customer;
    private CompanyAdmin $_admin;

    protected function setUp(): void
    {
        parent::setUp();

        Bus::fake();

        $this->_installment = Installment::factory()->create();
        $this->_transactionType = TransactionType::factory()->create();
        $this->_customer = Customer::factory()->create();
        $this->_admin = $this->createAdminUser();
    }

    #[Test]
    public function successCreate(): void
    {
        $transaction = new Transaction;
        $transaction->installment_id = $this->_installment->id;
        $transaction->transaction_type_id = $this->_transactionType->id;
        $transaction->customer_id = $this->_customer->id;
        $transaction->card_id = $cardId = Card::factory()->create()->id;
        $transaction->admin_id = $this->_admin->id;
        $transaction->amount = 1000;
        $transaction->paid_at = $paidAt = Carbon::now()->subDays(rand(1, 100));

        $transaction->save();

        $this->assertEquals($this->_installment->id, $transaction->installment_id);
        $this->assertEquals($this->_transactionType->id, $transaction->transaction_type_id);
        $this->assertEquals($this->_customer->id, $transaction->customer_id);
        $this->assertEquals($this->_admin->id, $transaction->admin_id);
        $this->assertEquals($cardId, $transaction->card_id);
        $this->assertEquals(1000, $transaction->amount);
        $this->assertEquals($paidAt->format('Y-m-d H:i:s'), $transaction->paid_at->format('Y-m-d H:i:s'));
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
        auth()->login($this->_admin);
        $transaction = new Transaction;
        $transaction->installment_id = $this->_installment->id;
        $transaction->transaction_type_id = $this->_transactionType->id;
        $transaction->customer_id = $this->_customer->id;
        $transaction->amount = 1000;

        $transaction->save();

        $this->assertEquals($this->_admin->id, $transaction->admin_id);
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
    public function doesNotIncrementInstallmentsPaidAmountWhenIsInitialFeeTrue(): void
    {
        $oldPaidAmount = (int) $this->_installment->paid_amount;
        $transaction = new Transaction;
        $transaction->installment_id = $this->_installment->id;
        $transaction->transaction_type_id = $this->_transactionType->id;
        $transaction->customer_id = $this->_customer->id;
        $transaction->amount = 1000;
        $transaction->is_initial_fee = true;
        $transaction->save();

        $this->assertEquals($oldPaidAmount, $this->_installment->refresh()->paid_amount);
    }

    #[Test]
    public function changesInstallmentStatusWhenAllAmountPaid(): void
    {
        $graphs = InstallmentGraphHelper::getGraphs($this->_installment);
        $amount = 0;
        foreach ($graphs as $graph) {
            $amount += $graph->amount - $graph->paid_amount;
        }
        $transaction = new Transaction;
        $transaction->installment_id = $this->_installment->id;
        $transaction->transaction_type_id = $this->_transactionType->id;
        $transaction->customer_id = $this->_customer->id;
        $transaction->amount = $amount;

        $transaction->save();

        $this->assertEquals(InstallmentStatusEnum::CLOSED, $this->_installment->refresh()->status);
    }

    #[Test]
    public function doesNotChangeInstallmentStatusWhenCurrentDebtIsPaid(): void
    {
        $this->_installment = Installment::factory()->create([
            'starts_in' => Carbon::now()->subMonth(),
            'status' => InstallmentStatusEnum::ACTIVE,
            'period' => 3,
        ]);
        $amount = $this->_installment->debtAmount;

        $transaction = new Transaction;
        $transaction->installment_id = $this->_installment->id;
        $transaction->transaction_type_id = $this->_transactionType->id;
        $transaction->customer_id = $this->_customer->id;
        $transaction->amount = $amount;
        $transaction->save();

        $this->_installment->refresh();
        $this->assertEquals(InstallmentStatusEnum::ACTIVE, $this->_installment->status);
    }

    #[Test]
    public function decrementsInstallmentPaidAmountWhenTransactionIsDeleted()
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

    #[Test]
    public function decrementsInstallmentPaidAmountWhenTransactionIsCanceled()
    {
        $transaction = Transaction::factory()->create([
            'installment_id' => $this->_installment->id,
            'amount' => 1000,
            'status' => TransactionStatusEnum::SUCCESS,
        ]);
        $this->_installment->refresh();
        $oldPaidAmount = $this->_installment->paid_amount;
        $transaction->update(['status' => TransactionStatusEnum::CANCELED]);

        $this->assertEquals($oldPaidAmount - 1000, $this->_installment->refresh()->paid_amount);
    }

    #[Test]
    public function doesNotDecrementInstallmentPaidAmountWhenTransactionIsCanceled()
    {
        // Transaction is not canceled just updated
        $transaction = Transaction::factory()->create([
            'installment_id' => $this->_installment->id,
            'amount' => 1000,
            'status' => TransactionStatusEnum::SUCCESS,
        ]);
        $this->_installment->refresh();
        $oldPaidAmount = $this->_installment->paid_amount;
        $transaction->update(['customer_id' => Customer::factory()->create()->id]);
        $this->assertEquals($oldPaidAmount, $this->_installment->refresh()->paid_amount);

        // Transaction is already canceled
        $transaction = Transaction::factory()->create([
            'installment_id' => $this->_installment->id,
            'amount' => 1000,
            'status' => TransactionStatusEnum::CANCELED,
        ]);
        $this->_installment->refresh();
        $oldPaidAmount = $this->_installment->paid_amount;
        $transaction->update(['status' => TransactionStatusEnum::CANCELED]);
        $this->assertEquals($oldPaidAmount, $this->_installment->refresh()->paid_amount);

        // Transaction is_initial_fee column is true so Installment paid amount does not decrement
        $transaction = Transaction::factory()->create([
            'installment_id' => $this->_installment->id,
            'amount' => 1000,
            'status' => TransactionStatusEnum::SUCCESS,
            'is_initial_fee' => true,
        ]);
        $this->_installment->refresh();
        $oldPaidAmount = $this->_installment->paid_amount;
        $transaction->update(['status' => TransactionStatusEnum::CANCELED]);
        $this->assertEquals($oldPaidAmount, $this->_installment->refresh()->paid_amount);
    }
}
