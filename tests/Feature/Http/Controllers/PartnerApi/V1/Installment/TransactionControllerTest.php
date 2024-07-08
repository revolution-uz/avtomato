<?php

namespace Tests\Feature\Http\Controllers\PartnerApi\V1\Installment;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Models\Company\Branch;
use App\Models\Company\CompanyAdmin;
use App\Models\Company\Installment;
use App\Models\Company\Rbac\Permission;
use App\Models\Company\Rbac\Role;
use App\Models\Company\Transaction;
use PHPUnit\Framework\Attributes\Test;
use Tests\Fakers\AtmosFaker;
use Tests\TestCase;

class TransactionControllerTest extends TestCase
{
    protected bool $tenancy = true;

    #[Test]
    public function index()
    {
        $branch1 = Branch::factory()->create();
        $branch2 = Branch::factory()->create();
        $admin = $this->createAdminUser();
        $installment1 = Installment::factory()->create(['branch_id' => $branch1->id]);
        $installment2 = Installment::factory()->create(['branch_id' => $branch2->id]);
        Transaction::factory(5)->create(['installment_id' => $installment1->id]);
        Transaction::factory(4)->create(['installment_id' => $installment2->id]);

        $response = $this->actingAs($admin)->getJson(route('transactions.index'));

        $response->assertOk();
        $this->assertIsArray($response->json('data'));
        $this->assertCount(9, $response->json('data'));
    }

    #[Test]
    public function indexRestrictedBranch()
    {
        $branch1 = Branch::factory()->create();
        $branch2 = Branch::factory()->create();
        $role = Role::factory()->create();
        $role->permissions()->attach(Permission::query()->where('slug', PermissionEnum::TRANSACTION_VIEW_ALL)->first()->id, ['branch_id' => $branch1->id]);
        $admin = CompanyAdmin::factory()->create(['role_id' => $role->id]);
        $installment1 = Installment::factory()->create(['branch_id' => $branch1->id]);
        $installment2 = Installment::factory()->create(['branch_id' => $branch2->id]);
        Transaction::factory(5)->create(['installment_id' => $installment1->id]);
        Transaction::factory(4)->create(['installment_id' => $installment2->id]);

        $response = $this->actingAs($admin)->getJson(route('transactions.index'));

        $response->assertOk();
        $this->assertIsArray($response->json('data'));
        $this->assertCount(5, $response->json('data'));
    }

    #[Test]
    public function single()
    {
        $customer = Transaction::factory()->create();

        $admin = $this->createAdminUser();
        $response = $this->actingAs($admin)->getJson(route('transactions.get-single', ['id' => $customer->id]));

        $response->assertOk();
        $this->assertEquals($customer->id, $response->json('data.id'));
    }

    #[Test]
    public function deleteTransactions()
    {
        $admin = $this->createAdminUser();

        $transaction = Transaction::factory()->create();

        $response = $this->actingAs($admin)->deleteJson(route('transactions.delete'), ['ids' => [$transaction->id]]);
        $response->assertOk();
    }

    #[Test]
    public function reverse()
    {
        $admin = $this->createAdminUser();

        AtmosFaker::getToken();
        AtmosFaker::reversePayment();
        $transaction = Transaction::factory()->create();
        $transaction->external_id = 1;
        $transaction->saveOrFail();

        $response = $this->actingAs($admin)->deleteJson(route('transactions.reverse', ['id' => $transaction->id]));
        $response->assertOk();
    }
}
