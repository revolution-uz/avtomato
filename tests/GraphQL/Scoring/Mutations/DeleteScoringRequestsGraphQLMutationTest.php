<?php

namespace Tests\GraphQL\Scoring\Mutations;

use App\Models\Company\Card;
use App\Models\Company\Customer;
use App\Models\Company\ScoringRequest;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class DeleteScoringRequestsGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private array $_ids = [1, 3, 4, 5, 6, 7, 8, 9];

    protected function setUp(): void
    {
        parent::setUp();
        Customer::query()->forceDelete();
        Card::query()->forceDelete();
        ScoringRequest::query()->forceDelete();
        ScoringRequest::factory(20)->create();
    }

    #[Test]
    public function successDelete(): void
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery(ids: $this->_ids)
            );
        $this->assertSuccessDeleteScoringResponse($response);
    }

    #[Test]
    public function errorValidation(): void
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery(ids: [1, 534, 543])
            );

        $response->assertOk();
        $this->assertEquals(trans('validation.exists', ['attribute' => 'ids.1']), $response->json('errors.0.extensions.validation')['ids.1'][0]);
        $this->assertEquals(trans('validation.exists', ['attribute' => 'ids.2']), $response->json('errors.0.extensions.validation')['ids.2'][0]);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery(ids: [2, 'a'])
            );
        $response->assertOk();
        $this->assertEquals('Variable "$ids" got invalid value "a" at "ids[1]"; Int cannot represent non-integer value: "a"', $response->json('errors.0.message'));
    }

    #[Test]
    public function accessDeniedValidation(): void
    {
        $response = $this
            ->actingAs($this->createSimpleAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery(ids: $this->_ids)
            );
        $response->assertOk();
        $this->assertEquals('Unauthorized', $response->json('errors.0.message'));
    }

    private function buildGraphQLQuery(array $ids): array
    {
        $query = [];
        $query['variables'] = ['ids' => $ids];
        $query['query'] = '
            mutation ($ids: [Int!]!) {
                deleteScoringRequests(ids: $ids)
            }
        ';

        return $query;
    }

    private function assertSuccessDeleteScoringResponse(TestResponse $response): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'deleteScoringRequests',
            ],
        ]);
        $scoringRequest = ScoringRequest::query()->whereIn('id', $this->_ids)->get();
        $this->assertEmpty($scoringRequest);
    }
}
