<?php

namespace Tests\Feature\Http\Controllers\PartnerApi\V1\Installment;

use App\Enums\Company\InstallmentStatusEnum;
use App\Enums\Company\InstallmentTypeEnum;
use App\Enums\Company\Rbac\PermissionEnum;
use App\Models\Company\Branch;
use App\Models\Company\CompanyAdmin;
use App\Models\Company\Customer;
use App\Models\Company\Installment;
use App\Models\Company\Rbac\Permission;
use App\Models\Company\Rbac\Role;
use App\Models\Company\Tariff;
use Carbon\Carbon;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class InstallmentControllerTest extends TestCase
{
    protected bool $tenancy = true;

    #[Test]
    public function index()
    {
        $branch1 = Branch::factory()->create();
        $branch2 = Branch::factory()->create();
        $role = Role::factory()->create();
        $role->permissions()->attach(Permission::query()->where('slug', PermissionEnum::INSTALLMENT_VIEW)->first()->id, ['branch_id' => $branch1->id]);
        $role->permissions()->attach(Permission::query()->where('slug', PermissionEnum::INSTALLMENT_VIEW_ALL)->first()->id);
        $admin = $this->createAdminUser();
        Installment::factory(5)->create(['branch_id' => $branch1->id]);
        Installment::factory(4)->create(['branch_id' => $branch2->id]);
        $response = $this->actingAs($admin)->getJson(route('installments.index'));

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
        $role->permissions()->attach(Permission::query()->where('slug', PermissionEnum::INSTALLMENT_VIEW_ALL)->first()->id);
        $role->permissions()->attach(Permission::query()->where('slug', PermissionEnum::INSTALLMENT_VIEW)->first()->id, ['branch_id' => $branch1->id]);
        $admin = CompanyAdmin::factory()->create(['role_id' => $role->id]);
        Installment::factory(5)->create(['branch_id' => $branch1->id]);
        Installment::factory(4)->create(['branch_id' => $branch2->id]);
        $response = $this->actingAs($admin)->getJson(route('installments.index'));

        $response->assertOk();
        $this->assertIsArray($response->json('data'));
        $this->assertCount(5, $response->json('data'));
    }

    #[Test]
    public function create()
    {
        $branch = Branch::factory()->create();
        $tariff = Tariff::factory()->create();
        $customer = Customer::factory()->create();
        $admin = $this->createAdminUser();
        $request = [
            'customer_id' => $customer->id,
            'branch_id' => $branch->id,
            'tariff_id' => $tariff->id,
            'type' => InstallmentTypeEnum::randomValue(),
            'period' => $tariff->max_period,
            'amount' => $tariff->max_amount,
            'initial_fee' => $tariff->max_initial_fee,
            'description' => 'Order description',
            'sms_notify' => false,
            'any_card_charge' => false,
            'is_blacklist' => false,
            'status' => InstallmentStatusEnum::randomValue(),
            'starts_in' => Carbon::now(),
            'next_pay_date' => Carbon::now(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
        $response = $this->actingAs($admin)->postJson(route('installments.create'), $request);
        $response->assertSuccessful();
        $response->assertJsonIsObject();
        $this->assertEquals($customer->id, $response->json('data.customer_id'));
        $this->assertEquals($branch->id, $response->json('data.branch_id'));
        $this->assertEquals($tariff->id, $response->json('data.tariff_id'));
    }

    #[Test]
    public function single()
    {
        $admin = $this->createAdminUser();
        $installment = Installment::factory()->create();
        $response = $this->actingAs($admin)->getJson(route('installments.single', ['id' => $installment->id]));
        $response->assertOk();
        $this->assertEquals($installment->customer_id, $response->json('data.customer_id'));
        $this->assertEquals($installment->tariff_id, $response->json('data.tariff_id'));
    }

    #[Test]
    public function update()
    {
        $tariff = Tariff::factory()->create();
        $admin = $this->createAdminUser();
        $installment = Installment::factory()->create();
        $request = [
            'tariff_id' => $tariff->id,
            'period' => $tariff->max_period,
            'amount' => $tariff->max_amount,
            'initial_fee' => $tariff->max_initial_fee,
            'starts_in' => Carbon::now(),
        ];
        $response = $this->actingAs($admin)->putJson(route('installments.update', ['id' => $installment->id]), $request);
        $installment->refresh();
        $response->assertSuccessful();
        $response->assertJsonIsObject();
        $this->assertEquals($installment->tariff_id, $response->json('data.tariff_id'));
    }
}
