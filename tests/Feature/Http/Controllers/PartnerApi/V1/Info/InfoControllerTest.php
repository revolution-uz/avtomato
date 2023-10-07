<?php

namespace Tests\Feature\Http\Controllers\PartnerApi\V1\Info;

use App\Models\Company\District;
use App\Models\Company\Mahalla;
use App\Models\Company\Region;
use Tests\TestCase;

class InfoControllerTest extends TestCase
{
    protected $tenancy = true;

    public function testSettings()
    {
        $user = $this->createAdminUser();
        $response = $this->actingAs($user)
            ->get(route('info.settings'));

        $response->assertOk();
    }

    public function testRegions()
    {
        Region::factory()->count(10)->create();
        $user = $this->createAdminUser();
        $response = $this->actingAs($user)
            ->get(route('info.regions'));

        $response->assertOk();
        $this->assertCount(10, $response->json('data'));
    }

    public function testDistricts()
    {
        District::factory()->count(10)->create();
        $user = $this->createAdminUser();
        $response = $this->actingAs($user)
            ->get(route('info.districts'));

        $response->assertOk();
        $this->assertCount(10, $response->json('data'));
    }

    public function testMahallas()
    {
        Mahalla::factory()->count(10)->create();
        $user = $this->createAdminUser();
        $response = $this->actingAs($user)
            ->get(route('info.mahallas'));

        $response->assertOk();
        $this->assertCount(10, $response->json('data'));
    }
}
