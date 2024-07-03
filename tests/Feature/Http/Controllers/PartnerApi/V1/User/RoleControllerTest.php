<?php

namespace Tests\Feature\Http\Controllers\PartnerApi\V1\User;

use App\Models\Company\Branch;
use App\Models\Company\Rbac\Role;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class RoleControllerTest extends TestCase
{
    protected bool $tenancy = true;

    #[Test]
    public function index()
    {
        $admin = $this->createAdminUser();
        $response = $this->actingAs($admin)->getJson(route('roles.index'));

        $response->assertOk();
        $this->assertCount(1, $response->json('data'));
    }

    #[Test]
    public function single()
    {
        $admin = $this->createAdminUser();
        $role = $admin->role;
        $response = $this->actingAs($admin)->getJson(route('roles.get-single', ['id' => $role->id]));

        $response->assertOk();
        $this->assertEquals($role->id, $response->json('data.id'));
        $this->assertEquals($role->name, $response->json('data.name'));
    }

    #[Test]
    public function create()
    {
        $admin = $this->createAdminUser();
        $response = $this->actingAs($admin)->postJson(route('roles.create'), [
            'name' => 'Test Role',
        ]);

        $response->assertSuccessful();
        $this->assertDatabaseHas('roles', [
            'name' => 'Test Role',
        ]);
        $this->assertEquals('Test Role', $response->json('data.name'));
    }

    #[Test]
    public function update()
    {
        $admin = $this->createAdminUser();
        $role = Role::factory()->create();
        $response = $this->actingAs($admin)->putJson(route('roles.update', ['id' => $role->id]), [
            'name' => 'Updated Role',
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('roles', [
            'name' => 'Updated Role',
        ]);
        $this->assertEquals('Updated Role', $response->json('data.name'));
    }

    #[Test]
    public function restrictUpdatingAdmin()
    {
        $admin = $this->createAdminUser();
        $response = $this->actingAs($admin)->putJson(route('roles.update', ['id' => $admin->role->id]), [
            'name' => 'Updated Role',
        ]);

        $response->assertForbidden();
        $this->assertEquals('You can\'t update admin role', $response->json('message'));
    }

    #[Test]
    public function attachPermissions()
    {
        $admin = $this->createAdminUser();
        $role = Role::factory()->create();
        $branch = Branch::factory()->create();
        $response = $this->actingAs($admin)->postJson(route('roles.attach-permissions', ['id' => $role->id]), [
            [
                'permission_id' => 1,
                'branch_id' => $branch->id,
                'enabled' => true,
            ],
            [
                'permission_id' => 2,
                'branch_id' => $branch->id,
                'enabled' => false,
            ],
            [
                'permission_id' => 3,
                'branch_id' => $branch->id,
                'enabled' => true,
            ],
        ]);

        $response->assertOk();
        $this->assertCount(2, $role->fresh()->permissions);
    }

    #[Test]
    public function remove()
    {
        $admin = $this->createAdminUser();
        $role = Role::factory()->create();
        $response = $this->actingAs($admin)->deleteJson(route('roles.delete', [
            'ids' => [$role->id],
        ]));

        $response->assertOk();
        $this->assertSoftDeleted($role);
    }

    #[Test]
    public function restrictDeletingAdmin()
    {
        $this->withoutExceptionHandling();

        $admin = $this->createAdminUser();
        $this->expectException(ValidationException::class);
        $this->actingAs($admin)->deleteJson(route('roles.delete', [
            'ids' => [$admin->role->id],
        ]));
    }
}
