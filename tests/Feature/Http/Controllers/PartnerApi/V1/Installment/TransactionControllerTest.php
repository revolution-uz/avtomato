<?php

namespace Feature\Http\Controllers\PartnerApi\V1\Installment;

use App\Models\Company\Transaction;
use Tests\TestCase;

class TransactionControllerTest extends TestCase
{
    protected $tenancy = true;

    /**
     * @test
     */
    public function index()
    {
        $admin = $this->createAdminUser();
        $response = $this->actingAs($admin)->get(route('transactions.index'));

        $response->assertOk();
        $this->assertIsArray($response->json('data'));
    }

    /**
     * @test
     */
    public function single()
    {
        $customer = Transaction::factory()->create();

        $admin = $this->createAdminUser();
        $response = $this->actingAs($admin)->get(route('transactions.get-single', ['id' => $customer->id]));

        $response->assertOk();
        $this->assertEquals($customer->id, $response->json('data.id'));
    }
}
