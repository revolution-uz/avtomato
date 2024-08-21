<?php

namespace Tests\GraphQL\Scoring\Queries;

use App\Models\Company\ScoringRequest;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class SingleCardScoringRequestGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private ScoringRequest $_scoring_request;

    protected function setUp(): void
    {
        parent::setUp();
        ScoringRequest::query()->forceDelete();
        $this->_scoring_request = ScoringRequest::factory()->create(['status' => 2]);
        $this->mockUrl('https://api.abrand.uz/partner/katm/get-request*', json_decode(file_get_contents(storage_path('../JSON-for-testing/credit-history-create-response.json')), true));

    }

    #[Test]
    public function successSingleScoringRequest()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery($this->_scoring_request->id)
            );
        $this->assertSuccessSingleScoringRequestResponse($response);
    }

    #[Test]
    public function errorSingleScoringRequest()
    {
        // not found
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery(99999999)
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'id']), $response->json('errors.0.extensions.validation.id.0'));

        // deleted
        $this->_scoring_request->delete();
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery($this->_scoring_request->id)
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'id']), $response->json('errors.0.extensions.validation.id.0'));
    }

    private function buildGraphQLQuery($id): array
    {
        $query = [];
        $query['variables'] = ['id' => $id];
        $query['query'] = '
            query ($id: Int!) {
                singleCardScoringRequest(id: $id) {
                    id
                    ext_id
                    status
                    type
                    client_code
                    customer_id
                    card_id
                    created_at
                    updated_at
                    request {
                        pan
                        expiry
                    }
                     response {
                        report {
                            month
                            full_amount
                            details {
                                description
                                type_code
                                amount
                            }
                        }
                    }
                }
            }
        ';

        return $query;
    }

    private function assertSuccessSingleScoringRequestResponse(TestResponse $response): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'singleCardScoringRequest' => [
                    'id',
                    'ext_id',
                    'status',
                    'type',
                    'client_code',
                    'customer_id',
                    'card_id',
                    'created_at',
                    'updated_at',
                    'request' => [
                        'pan',
                        'expiry',
                    ],
                    'response' => [
                        'report',
                    ],
                ],
            ],
        ]);
        $scoring = ScoringRequest::query()->find($response->json('data.singleCardScoringRequest.id'));
        $this->assertArrayHasKey('id', $response->json('data.singleCardScoringRequest'));
        $this->assertEquals($scoring->id, $response->json('data.singleCardScoringRequest.id'));
    }
}
