<?php

namespace Tests\Feature\Http\Controllers\PartnerApi\V1\Management;

use App\Models\Company\Branch;
use App\Models\Company\User;
use Tests\TestCase;

class BranchControllerTest extends TestCase
{
    protected $tenancy = true;

    public function testIndex()
    {
        Branch::factory(5)->create();

        $user = $this->createAdminUser();
        $response = $this->actingAs($user, 'web')->get(route('branches.index'));

        $response->assertOk();
        $this->assertNotEmpty($response->json('data'));
        $this->assertCount(5, $response->json('data'));
    }

    public function testSingle()
    {
        $branch = Branch::factory()->create();

        $user = $this->createAdminUser();
        $response = $this->actingAs($user, 'web')->get(route('branches.get-single', $branch->id));

        $response->assertOk();
        $this->assertNotEmpty($response->json('data'));
        $this->assertEquals($branch->id, $response->json('data.id'));
    }

    public function testCreate()
    {
        $user = $this->createAdminUser();
        $response = $this->actingAs($user, 'web')->post(route('branches.create'), [
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

    public function testUpdate()
    {
        $branch = Branch::factory()->create();

        $user = $this->createAdminUser();
        $response = $this->actingAs($user, 'web')->put(route('branches.update', $branch->id), [
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

    public function testDelete()
    {
        $branch = Branch::factory()->create();

        $user = User::factory()->create();
        $response = $this->actingAs($user, 'web')->delete(route('branches.delete'), [
            'ids' => [$branch->id],
        ]);

        $response->assertOk();
        $this->assertTrue($response->json('data'));
    }
}
