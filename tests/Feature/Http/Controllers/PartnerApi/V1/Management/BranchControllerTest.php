<?php

namespace Tests\Feature\Http\Controllers\PartnerApi\V1\Management;

use App\Models\Company\Branch;
use Tests\TestCase;

class BranchControllerTest extends TestCase
{
    protected $tenancy = true;

    public function testIndex()
    {
        Branch::factory(5)->create();

        $admin = $this->createAdminUser();
        $response = $this->actingAs($admin, 'web')->get(route('branches.index'));

        $response->assertOk();
        $this->assertNotEmpty($response->json('data'));
        $this->assertCount(5, $response->json('data'));
    }

    /**
     * @test
     */
    public function single()
    {
        $branch = Branch::factory()->create();

        $admin = $this->createAdminUser();
        $response = $this->actingAs($admin, 'web')->get(route('branches.get-single', ['id' => $branch->id]));

        $response->assertOk();
        $this->assertNotEmpty($response->json('data'));
        $this->assertEquals($branch->id, $response->json('data.id'));
    }

    /**
     * @test
     */
    public function create()
    {
        $admin = $this->createAdminUser();
        $response = $this->actingAs($admin, 'web')->post(route('branches.create'), [
            'name' => 'Test Branch',
            'address' => 'Test Address',
            'inn' => '123456789',
        ]);

        $response->assertOk();
        $this->assertNotEmpty($response->json('data'));
        $this->assertEquals('Test Branch', $response->json('data.name'));
        $this->assertEquals('Test Address', $response->json('data.address'));
        $this->assertEquals('123456789', $response->json('data.inn'));
    }

    /**
     * @test
     */
    public function update()
    {
        $branch = Branch::factory()->create();

        $admin = $this->createAdminUser();
        $response = $this->actingAs($admin, 'web')->put(route('branches.update', ['id' => $branch->id]), [
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

    /**
     * @test
     */
    public function remove()
    {
        $branch = Branch::factory()->create();
        $admin = $this->createAdminUser();
        $response = $this->actingAs($admin, 'web')->delete(route('branches.delete'), [
            'ids' => [$branch->id],
        ]);

        $response->assertOk();
        $this->assertTrue($response->json('data'));
    }
}
