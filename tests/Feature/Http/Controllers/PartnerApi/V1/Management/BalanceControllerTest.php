<?php

namespace Tests\Feature\Http\Controllers\PartnerApi\V1\Management;

use Tests\Fakers\AtmosFaker;
use Tests\TestCase;

class BalanceControllerTest extends TestCase
{
    protected bool $tenancy = true;

    /**
     * @test
     */
    public function index(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)
            ->getJson(route('balance.fund'));

        $response->assertOk();

        $this->assertIsArray($response->json('data'));
    }

    /**
     * @test
     */
    public function fill(): void
    {
        $admin = $this->createAdminUser();

        AtmosFaker::fakeResponse('https://partner.atmos.uz/bind-card/init', '../JSON-for-testing/atmos/bind-card-init.json', 200);

        $request = [
            'card_number' => 9860082443467252,
            'expiry' => 2808,
        ];

        $response = $this->actingAs($admin)->postJson('/api/v1/balance/fill', $request);

        $response->assertSuccessful();

        $this->assertIsArray($response->json());
    }

    /**
     * @test
     */
    public function confirmPayment(): void
    {
        $admin = $this->createAdminUser();

        AtmosFaker::fakeResponse('https://partner.atmos.uz/bind-card/confirm', '../JSON-for-testing/atmos/bind-card-confirm.json', 200);

        $request = [
            'transaction_id' => 1,
            'amount' => 1000000,
            'otp' => 123456,
            'card_number' => 9860082443467252,
            'phone_number' => '+998941541995',
        ];

        $response = $this->actingAs($admin)->postJson('/api/v1/balance/confirm-payment', $request);

        $response->assertSuccessful();

        $this->assertIsArray($response->json());
    }
}
