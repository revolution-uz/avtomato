<?php

namespace Tests\Feature\Http\Controllers\PartnerApi\V1\Installment;

use App\Models\Company\Tariff;
use Tests\TestCase;

class TariffControllerTest extends TestCase
{
    protected $tenancy = true;

    /**
     * @test
     */
    public function index()
    {
        Tariff::factory(5)->create();
        $admin = $this->createAdminUser();
        $response = $this->actingAs($admin)->getJson(route('tariffs.index'));

        $response->assertOk();
        $this->assertCount(5, $response->json('data'));
    }

    /**
     * @test
     */
    public function getSingle()
    {
        $tariff = Tariff::factory()->create();
        $admin = $this->createAdminUser();
        $response = $this->actingAs($admin)->getJson(route('tariffs.get-single', ['id' => $tariff->id]));

        $response->assertOk();
        $this->assertEquals($tariff->id, $response->json('data.id'));
    }

    /**
     * @test
     */
    public function create()
    {
        $admin = $this->createAdminUser();
        $response = $this->actingAs($admin)->postJson(route('tariffs.create', [
            'name' => 'New tariff',
            'is_annual' => true,
            'percent' => 10,
            'min_period' => 1,
            'max_period' => 12,
            'min_amount' => 1000,
            'max_amount' => 10000,
            'min_initial_fee' => 100,
            'max_initial_fee' => 1000,
        ]));

        $response->assertSuccessful();
        $this->assertDatabaseHas('tariffs', [
            'name' => 'New tariff',
            'is_annual' => true,
            'percent' => 10,
            'min_period' => 1,
            'max_period' => 12,
            'min_amount' => 1000,
            'max_amount' => 10000,
            'min_initial_fee' => 100,
            'max_initial_fee' => 1000,
        ]);
    }

    /**
     * @test
     */
    public function update()
    {
        $tariff = Tariff::factory()->create();
        $admin = $this->createAdminUser();
        $response = $this->actingAs($admin)->putJson(route('tariffs.update', ['id' => $tariff->id]), [
            'name' => 'Updated tariff',
            'is_annual' => true,
            'percent' => 10,
            'min_period' => 1,
            'max_period' => 12,
            'min_amount' => 1000,
            'max_amount' => 10000,
            'min_initial_fee' => 100,
            'max_initial_fee' => 1000,
        ]);

        $response->assertOk();
        $this->assertDatabaseHas('tariffs', [
            'name' => 'Updated tariff',
            'is_annual' => true,
            'percent' => 10,
            'min_period' => 1,
            'max_period' => 12,
            'min_amount' => 1000,
            'max_amount' => 10000,
            'min_initial_fee' => 100,
            'max_initial_fee' => 1000,
        ]);
    }

    /**
     * @test
     */
    public function remove()
    {
        $tariff = Tariff::factory()->create();
        $admin = $this->createAdminUser();
        $response = $this->actingAs($admin)->deleteJson(route('tariffs.delete', [
            'ids' => [$tariff->id],
        ]));

        $response->assertOk();
        $this->assertSoftDeleted($tariff);
    }
}
