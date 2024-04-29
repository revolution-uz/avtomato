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
            ->getJson(route('balance.index'));

        $response->assertOk();

        $this->assertIsArray($response->json('data'));
    }

    /**
     * @test
     */
    public function fill(): void
    {
        $admin = $this->createAdminUser();
        AtmosFaker::getToken();
        AtmosFaker::attachCard();

        $request = [
            'number' => '9860082443467252',
            'expiry' => '2808',
        ];

        $response = $this->actingAs($admin)->postJson(route('balance.fill'), $request);

        $response->assertSuccessful();

        $this->assertIsArray($response->json());
    }

    /**
     * @test
     */
    public function confirmPayment(): void
    {
        $admin = $this->createAdminUser();
        AtmosFaker::getToken();
        AtmosFaker::confirmCard();
        AtmosFaker::createPayment();
        AtmosFaker::preConfirmPayment();
        AtmosFaker::confirmPayment();

        $request = [
            'transaction_id' => 1,
            'amount' => 1000000,
            'otp' => '123456',
            'number' => '9860082443467252',
            'phone' => '+998900222222',
        ];

        $response = $this->actingAs($admin)->postJson(route('balance.confirm-payment'), $request);

        //        $response->assertSuccessful();
    }
}
