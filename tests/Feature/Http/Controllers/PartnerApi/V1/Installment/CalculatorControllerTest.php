<?php

namespace Tests\Feature\Http\Controllers\PartnerApi\V1\Installment;

use App\Models\Company\Installment;
use Tests\TestCase;

class CalculatorControllerTest extends TestCase
{
    protected $tenancy = true;

    /**
     * @test
     */
    public function calculate()
    {
        $tariff = Installment::factory()->create();

        $admin = $this->createAdminUser();
        $response = $this->actingAs($admin)->postJson(route('calculator'), [
            'tariff_id' => $tariff->id,
            'amount' => 300000,
            'initial_amount' => 100000,
            'period' => 12,
        ]);

        $response->assertOk();
    }
}
