<?php

namespace Http\Controllers\PartnerApi\V1\Scoring\UzCard;

use App\Enums\Company\Scoring\ScoringRequestTypeEnum;
use App\Models\Company\ScoringRequest;
use Tests\Fakers\PlumFaker;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class UzCardScoringControllerTest extends TestCase
{
    protected bool $tenancy = true;

    #[Test]
    public function index(): void
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)
            ->getJson(route('uzcard.index'));

        $response->assertOk();

        $this->assertIsArray($response->json('data'));
    }

    #[Test]
    public function create(): void
    {
        $admin = $this->createAdminUser();

        PlumFaker::fakeResponse('https://api.abrand.uz*', '../JSON-for-testing/card-scoring-create-response.json', 201);

        $request = [
            'number' => 8600312938450000,
            'expiry' => 2704,
        ];

        $response = $this->actingAs($admin)->postJson('/api/v1/scoring/uzcard/create', $request);

        $response->assertSuccessful();

        $this->assertDatabaseHas('scoring_requests', ['request->number' => $request['number']]);

        $this->assertDatabaseHas('scoring_requests', ['request->expiry' => $request['expiry']]);

        $this->assertIsArray($response->json('data'));
    }

    #[Test]
    public function checkValidation(): void
    {
        $admin = $this->createAdminUser();

        PlumFaker::fakeResponse('https://api.abrand.uz*', '../JSON-for-testing/card-scoring-create-response.json');

        $response = $this->actingAs($admin)->postJson('/api/v1/scoring/uzcard/create', [
            'number' => 860031293845000,
            'expiry' => 042,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['number', 'expiry']);
    }

    #[Test]
    public function show(): void
    {
        $admin = $this->createAdminUser();

        PlumFaker::fakeResponse('https://api.abrand.uz*', '../JSON-for-testing/card-scoring-create-response.json', 201);

        $request = [
            'number' => 8600312938450000,
            'expiry' => 2704,
        ];

        $this->actingAs($admin)->postJson('/api/v1/scoring/uzcard/create', $request);
        $newScoringRequest = ScoringRequest::query()
            ->where('type', ScoringRequestTypeEnum::UZCARD)
            ->findOrFail(1);

        $response = $this->actingAs($admin)->getJson(route('uzcard.read', ['id' => $newScoringRequest->id]));

        $response->assertOk();
        $this->assertEquals($newScoringRequest->request->number, $response->json('data')['request']['number']);
    }

    #[Test]
    public function deleteUzCardScoring(): void
    {
        $admin = $this->createAdminUser();

        PlumFaker::fakeResponse('https://api.abrand.uz*', '../JSON-for-testing/card-scoring-create-response.json', 200);

        $request = [
            'number' => 8600312938450000,
            'expiry' => 2704,
        ];

        $this->actingAs($admin)->postJson('/api/v1/scoring/uzcard/create', $request);

        $newScoringRequest = ScoringRequest::query()
            ->where('type', ScoringRequestTypeEnum::UZCARD)
            ->where('id', 1)
            ->firstOrFail();

        $response = $this->actingAs($admin)->deleteJson(route('uzcard.delete'), [
            'ids' => [$newScoringRequest->id],
        ]);

        $response->assertOk();
        $this->assertTrue($response->json('data.success'));

        // Check if the row has been soft deleted
        $this->assertSoftDeleted('scoring_requests', ['id' => $newScoringRequest->id]);
    }
}
