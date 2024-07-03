<?php

namespace Http\Controllers\PartnerApi\V1\Scoring\HUMO;

use App\Enums\Company\Scoring\ScoringRequestTypeEnum;
use App\Models\Company\ScoringRequest;
use App\Models\Company\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\Fakers\PlumFaker;
use Tests\TestCase;

class HumoScoringControllerTest extends TestCase
{
    protected bool $tenancy = true;

    #[Test]
    public function index(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)
            ->getJson(route('humo.index'));

        $response->assertOk();

        $this->assertIsArray($response->json('data'));
    }

    #[Test]
    public function create(): void
    {
        $admin = $this->createAdminUser();

        PlumFaker::fakeResponse('https://api.abrand.uz/katm/humo', '../JSON-for-testing/card-scoring-create-response.json', 201);

        $request = [
            'number' => 9860082443467252,
            'expiry' => 2808,
        ];

        $response = $this->actingAs($admin)->postJson('/api/v1/scoring/humo/create', $request);

        $response->assertSuccessful();

        $this->assertDatabaseHas('scoring_requests', ['request->number' => $request['number']]);

        $this->assertDatabaseHas('scoring_requests', ['request->expiry' => $request['expiry']]);

        $this->assertDatabaseHas(
            'activity_logs',
            [
                'description' => 'created',
                'subject_type' => ScoringRequest::class,
                'subject_id' => $response->json('data.id'),
                'causer_type' => User::class,
                'causer_id' => $admin->id,
            ]
        );

        $this->assertIsArray($response->json('data'));
    }

    #[Test]
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

    #[Test]
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

    #[Test]
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
