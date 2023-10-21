<?php

namespace Feature\Http\Controllers\PartnerApi\V1\Customer;

use App\Models\Company\Card;
use Tests\TestCase;

class CardControllerTest extends TestCase
{
    protected $tenancy = true;

    /**
     * @test
     */
    public function index()
    {
        $admin = $this->createAdminUser();
        $response = $this->actingAs($admin)->get(route('cards.index'));

        $response->assertOk();
        $this->assertIsArray($response->json('data'));
    }

    /**
     * @test
     */
    public function single()
    {
        $customer = Card::factory()->create();

        $admin = $this->createAdminUser();
        $response = $this->actingAs($admin)->get(route('cards.get-single', ['id' => $customer->id]));

        $response->assertOk();
        $this->assertEquals($customer->id, $response->json('data.id'));
    }
}
