<?php

namespace Tests\Feature\Http\Controllers\PartnerApi\V1\Management;

use App\Enums\Company\Scoring\ScoringRequestTypeEnum;
use App\Models\Company\ScoringRequest;
use Tests\Fakers\AtmosFaker;
use Tests\Fakers\PlumFaker;
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

    /**
     * @test
     */
    public function checkValidation(): void
    {
        $admin = $this->createAdminUser();

        PlumFaker::fakeResponse('https://api.abrand.uz/katm/humo', '../JSON-for-testing/card-scoring-create-response.json');

        $response = $this->actingAs($admin)->postJson('/api/v1/scoring/humo/create', [
            'number' => 986008244346722,
            'expiry' => 280,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['number', 'expiry']);
    }

    /**
     * @test
     */
    public function show(): void
    {
        $admin = $this->createAdminUser();

        PlumFaker::fakeResponse('https://api.abrand.uz/partner/katm/humo', '../JSON-for-testing/card-scoring-create-response.json', 201);

        $request = [
            'number' => 9860082443467252,
            'expiry' => 2808,
        ];

        $this->actingAs($admin)->postJson('/api/v1/scoring/humo/create', $request);

        $newScoringRequest = ScoringRequest::query()
            ->where('type', ScoringRequestTypeEnum::HUMO)
            ->where('id', 1)
            ->firstOrFail();

        $response = $this->actingAs($admin)->getJson(route('humo.read', ['id' => $newScoringRequest->id]));
        $response->assertOk();
        $this->assertEquals($newScoringRequest->request->number, $response->json('data')['request']['number']);
    }

    /**
     * @test
     */
    public function deleteHumoScoring(): void
    {
        $admin = $this->createAdminUser();

        PlumFaker::fakeResponse('https://api.abrand.uz/katm/humo', '../JSON-for-testing/card-scoring-create-response.json', 201);

        $request = [
            'number' => 9860082443467252,
            'expiry' => 2808,
        ];

        $this->actingAs($admin)->postJson('/api/v1/scoring/humo/create', $request);

        $newScoringRequest = ScoringRequest::query()
            ->where('type', ScoringRequestTypeEnum::HUMO)
            ->where('id', 1)
            ->firstOrFail();

        $response = $this->actingAs($admin)->deleteJson(route('humo.delete'), [
            'ids' => [$newScoringRequest->id],
        ]);

        $response->assertOk();
        $this->assertTrue($response->json('data.success'));

        // Check if the row has been soft deleted
        $this->assertSoftDeleted('scoring_requests', ['id' => $newScoringRequest->id]);
    }
}
