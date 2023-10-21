<?php

namespace Feature\Http\Controllers\PartnerApi\V1\Installment;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Models\Company\Branch;
use App\Models\Company\Installment;
use App\Models\Company\Rbac\Permission;
use App\Models\Company\Rbac\Role;
use App\Models\Company\Transaction;
use App\Models\Company\User;
use Tests\TestCase;

class TransactionControllerTest extends TestCase
{
    protected $tenancy = true;

    /**
     * @test
     */
    public function index()
    {
        $branch1 = Branch::factory()->create();
        $branch2 = Branch::factory()->create();
        $admin = $this->createAdminUser();
        $installment1 = Installment::factory()->create(['branch_id' => $branch1->id]);
        $installment2 = Installment::factory()->create(['branch_id' => $branch2->id]);
        Transaction::factory(5)->create(['installment_id' => $installment1->id]);
        Transaction::factory(4)->create(['installment_id' => $installment2->id]);

        $response = $this->actingAs($admin)->get(route('transactions.index'));

        $response->assertOk();
        $this->assertIsArray($response->json('data'));
        $this->assertCount(9, $response->json('data'));
    }

    /**
     * @test
     */
    public function indexRestrictedBranch()
    {
        $branch1 = Branch::factory()->create();
        $branch2 = Branch::factory()->create();
        $role = Role::factory()->create();
        $role->permissions()->attach(Permission::query()->where('slug', PermissionEnum::TRANSACTION_VIEW_ALL)->first()->id, ['branch_id' => $branch1->id]);
        $user = User::factory()->create(['role_id' => $role->id]);
        $installment1 = Installment::factory()->create(['branch_id' => $branch1->id]);
        $installment2 = Installment::factory()->create(['branch_id' => $branch2->id]);
        Transaction::factory(5)->create(['installment_id' => $installment1->id]);
        Transaction::factory(4)->create(['installment_id' => $installment2->id]);

        $response = $this->actingAs($user)->get(route('transactions.index'));

        $response->assertOk();
        $this->assertIsArray($response->json('data'));
        $this->assertCount(5, $response->json('data'));
    }

    /**
     * @test
     */
    public function single()
    {
        $customer = Transaction::factory()->create();

        $admin = $this->createAdminUser();
        $response = $this->actingAs($admin)->get(route('transactions.get-single', ['id' => $customer->id]));

        $response->assertOk();
        $this->assertEquals($customer->id, $response->json('data.id'));
    }
}
