<?php

namespace Feature\Http\Controllers\PartnerApi\V1\Installment;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Models\Company\Branch;
use App\Models\Company\Installment;
use App\Models\Company\Rbac\Permission;
use App\Models\Company\Rbac\Role;
use App\Models\Company\User;
use Tests\TestCase;

class InstallmentControllerTest extends TestCase
{
    protected $tenancy = true;

    /**
     * @test
     */
    public function index()
    {
        $branch1 = Branch::factory()->create();
        $branch2 = Branch::factory()->create();
        $role = Role::factory()->create();
        $role->permissions()->attach(Permission::query()->where('slug', PermissionEnum::INSTALLMENT_VIEW_ALL)->first()->id, ['branch_id' => $branch1->id]);
        $admin = $this->createAdminUser();
        Installment::factory(5)->create(['branch_id' => $branch1->id]);
        Installment::factory(4)->create(['branch_id' => $branch2->id]);
        $response = $this->actingAs($admin)->get(route('installments.index'));

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
        $role->permissions()->attach(Permission::query()->where('slug', PermissionEnum::INSTALLMENT_VIEW_ALL)->first()->id, ['branch_id' => $branch1->id]);
        $user = User::factory()->create(['role_id' => $role->id]);
        Installment::factory(5)->create(['branch_id' => $branch1->id]);
        Installment::factory(4)->create(['branch_id' => $branch2->id]);
        $response = $this->actingAs($user)->get(route('installments.index'));

        $response->assertOk();
        $this->assertIsArray($response->json('data'));
        $this->assertCount(5, $response->json('data'));
    }
}
