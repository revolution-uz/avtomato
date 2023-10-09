<?php

namespace Tests\Feature\Http\Controllers\PartnerApi\V1\Info;

use App\Models\Company\District;
use App\Models\Company\Mahalla;
use App\Models\Company\Region;
use Tests\TestCase;

class InfoControllerTest extends TestCase
{
    protected $tenancy = true;

    /**
     * @test
     */
    public function settings()
    {
        $admin = $this->createAdminUser();
        $response = $this->actingAs($admin)
            ->get(route('info.settings'));

        $response->assertOk();
    }

    /**
     * @test
     */
    public function regions()
    {
        Region::factory()->count(10)->create();
        $admin = $this->createAdminUser();
        $response = $this->actingAs($admin)
            ->get(route('info.regions'));

        $response->assertOk();
        $this->assertCount(10, $response->json('data'));
    }

    /**
     * @test
     */
    public function districts()
    {
        District::factory()->count(10)->create();
        $admin = $this->createAdminUser();
        $response = $this->actingAs($admin)
            ->get(route('info.districts'));

        $response->assertOk();
        $this->assertCount(10, $response->json('data'));
    }

    /**
     * @test
     */
    public function mahallas()
    {
        Mahalla::factory()->count(10)->create();
        $admin = $this->createAdminUser();
        $response = $this->actingAs($admin)
            ->get(route('info.mahallas'));

        $response->assertOk();
        $this->assertCount(10, $response->json('data'));
    }
}
