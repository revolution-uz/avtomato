<?php

namespace Tests\Feature\Http\Controllers\PartnerApi\V1\Management;

use App\Models\Company\Branch;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BranchControllerTest extends TestCase
{
    protected bool $tenancy = true;

    #[Test]
    public function index()
    {
        Branch::factory(5)->create();

        $admin = $this->createAdminUser();
        $response = $this->actingAs($admin)->getJson(route('branches.index'));

        $response->assertOk();
        $this->assertNotEmpty($response->json('data'));
        $this->assertCount(5, $response->json('data'));
    }

    #[Test]
    public function single()
    {
        $branch = Branch::factory()->create();

        $admin = $this->createAdminUser();
        $response = $this->actingAs($admin)->getJson(route('branches.get-single', ['id' => $branch->id]));

        $response->assertOk();
        $this->assertNotEmpty($response->json('data'));
        $this->assertEquals($branch->id, $response->json('data.id'));
    }

    #[Test]
    public function create()
    {
        $admin = $this->createAdminUser();
        $response = $this->actingAs($admin)->postJson(route('branches.create'), [
            'name' => 'Test Branch',
            'address' => 'Test Address',
            'inn' => '123456789',
        ]);

        $response->assertSuccessful();
        $this->assertNotEmpty($response->json('data'));
        $this->assertEquals('Test Branch', $response->json('data.name'));
        $this->assertEquals('Test Address', $response->json('data.address'));
        $this->assertEquals('123456789', $response->json('data.inn'));
    }

    #[Test]
    public function update()
    {
        $branch = Branch::factory()->create();

        $admin = $this->createAdminUser();
        $response = $this->actingAs($admin)->putJson(route('branches.update', ['id' => $branch->id]), [
            'name' => 'Updated Branch',
            'address' => 'Updated Address',
            'inn' => '123456789',
        ]);

        $response->assertOk();
        $this->assertNotEmpty($response->json('data'));
        $this->assertEquals('Updated Branch', $response->json('data.name'));
        $this->assertEquals('Updated Address', $response->json('data.address'));
        $this->assertEquals('123456789', $response->json('data.inn'));
    }

    #[Test]
    public function remove()
    {
        $branch = Branch::factory()->create();
        $admin = $this->createAdminUser();
        $response = $this->actingAs($admin)->deleteJson(route('branches.delete'), [
            'ids' => [$branch->id],
        ]);

        $response->assertOk();
        $this->assertTrue($response->json('data.success'));
    }
}
