<?php

namespace Tests\Feature\Http\Controllers\PartnerApi\V1\Installment;

use App\Models\Company\Tariff;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CalculatorControllerTest extends TestCase
{
    protected bool $tenancy = true;

    #[Test]
    public function calculate()
    {
        $tariff = Tariff::factory()->create();

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
