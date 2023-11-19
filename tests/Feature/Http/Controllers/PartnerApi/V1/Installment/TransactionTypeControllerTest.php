<?php

namespace Tests\Feature\Http\Controllers\PartnerApi\V1\Installment;

use App\Models\Company\TransactionType;
use Tests\TestCase;

class TransactionTypeControllerTest extends TestCase
{
    protected bool $tenancy = true;

    /**
     * @test
     */
    public function index()
    {
        $admin = $this->createAdminUser();
        $response = $this->actingAs($admin)->getJson(route('transaction-types.index'));

        $response->assertOk();
        $this->assertIsArray($response->json('data'));
    }

    /**
     * @test
     */
    public function single()
    {
        $type = TransactionType::factory()->create();

        $admin = $this->createAdminUser();
        $response = $this->actingAs($admin)->getJson(route('transaction-types.get-single', ['id' => $type->id]));

        $response->assertOk();
        $this->assertEquals($type->id, $response->json('data.id'));
    }
}
