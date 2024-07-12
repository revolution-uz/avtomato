<?php

namespace Tests\GraphQL\Scoring\Queries;

use App\DTOs\Scoring\Plum\Mib\MibInfoCreateRequestDTO;
use App\DTOs\Scoring\Plum\Mib\MibInfoResponseDTO;
use App\Enums\Company\Scoring\ScoringRequestStatusEnum;
use App\Enums\Company\Scoring\ScoringRequestTypeEnum;
use App\Models\Company\ScoringRequest;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class SingleDebtScoringRequestGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private ScoringRequest $_scoring_request;

    protected function setUp(): void
    {
        parent::setUp();
        ScoringRequest::query()->forceDelete();
        $this->_scoring_request = ScoringRequest::factory()->create(
            [
                'status' => ScoringRequestStatusEnum::SUCCESS,
                'type' => ScoringRequestTypeEnum::PLUM_MIB_INFO,
                'request' => new MibInfoCreateRequestDTO('21710025140075', '+998938612345'),
                'response' => new MibInfoResponseDTO(1),
            ]
        );
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
        singleDebtScoringRequest(id: $id) {
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
              pinfl
              phone
            }
             response {
                debtBal
            }
        }
    }';

        return $query;
    }

    private function assertSuccessSingleScoringRequestResponse(TestResponse $response): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'singleDebtScoringRequest' => [
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
                        'pinfl',
                        'phone',
                    ],
                    'response' => [
                        'debtBal',
                    ],
                ],
            ],
        ]);
        $scoring = ScoringRequest::query()->find($response->json('data.singleDebtScoringRequest.id'));
        $this->assertArrayHasKey('id', $response->json('data.singleDebtScoringRequest'));
        $this->assertEquals($scoring->id, $response->json('data.singleDebtScoringRequest.id'));
    }
}
