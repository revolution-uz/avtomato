<?php

namespace Tests\Unit\Commands;

use App\Models\Company\Transaction;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AddBranchIdToOldAutoPayTest extends TestCase
{
    protected bool $tenancy = true;
    protected bool $multiTenancies = true;

    #[Test]
    public function addBranchIdToOldAutoPay()
    {
        Transaction::factory(5)->create(['branch_id' => null]);
        $this->artisan('autopay:add-branch_id');
        $this->assertDatabaseCount('transactions', 5);
        foreach (Transaction::all() as $transaction) {
            $this->assertNotNull($transaction->branch_id);
            $this->assertEquals($transaction->branch_id, $transaction->installment->branch_id);
        }
    }
}
