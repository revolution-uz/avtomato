<?php

namespace Http\Controllers\PartnerApi\V1\Scoring\HUMO;

use App\Enums\Company\Scoring\ScoringRequestTypeEnum;
use App\Models\Company\ScoringRequest;
use Tests\Fakers\PlumFaker;
use Tests\TestCase;

class HumoScoringControllerTest extends TestCase
{
    protected bool $tenancy = true;

    /**
     * @test
     */
    public function index(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)
            ->getJson(route('humo.index'));

        $response->assertOk();

        $this->assertIsArray($response->json('data'));
    }

    /**
     * @test
     */
    public function create(): void
    {
        $admin = $this->createAdminUser();

        PlumFaker::fakeResponse('https://api.abrand.uz*', '../JSON-for-testing/humo.create.json', 201);

        $request = [
            'number' => 9860082443467252,
            'expiry' => 2808,
        ];

        $response = $this->actingAs($admin)->postJson('/api/v1/scoring/humo/create', $request);

        $response->assertSuccessful();

        $this->assertDatabaseHas('scoring_requests', ['request->number' => $request['number']]);

        $this->assertDatabaseHas('scoring_requests', ['request->expiry' => $request['expiry']]);

        $this->assertIsArray($response->json('data'));
    }

    /**
     * @test
     */
    public function checkValidation(): void
    {
        $admin = $this->createAdminUser();

        PlumFaker::fakeResponse('https://api.abrand.uz*', '../JSON-for-testing/humo.create.json');

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

        PlumFaker::fakeResponse('https://api.abrand.uz*', '../JSON-for-testing/humo.create.json', 201);

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

        PlumFaker::fakeResponse('https://api.abrand.uz*', '../JSON-for-testing/humo.create.json', 201);

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
