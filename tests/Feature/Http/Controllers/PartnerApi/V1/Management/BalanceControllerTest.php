<?php

namespace Tests\Feature\Http\Controllers\PartnerApi\V1\Management;

use Tests\Fakers\AtmosFaker;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class BalanceControllerTest extends TestCase
{
    protected bool $tenancy = true;

    #[Test]
    public function funds(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)
            ->getJson(route('balance.funds'));

        $response->assertOk();

        $this->assertIsArray($response->json('data'));
    }

    #[Test]
    public function charges(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)
            ->getJson(route('balance.charges'));

        $response->assertOk();

        $this->assertIsArray($response->json('data'));
    }

    #[Test]
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

    #[Test]
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

        $response->assertSuccessful();
    }
}
