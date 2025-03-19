<?php

namespace Tests\GraphQL\Scoring\Queries;

use App\DTOs\Scoring\RoyxatCreditHistory\RoyxatCreditHistoryLoanDTO;
use App\DTOs\Scoring\RoyxatCreditHistory\RoyxatCreditHistoryResponseDTO;
use App\Enums\Company\Scoring\ScoringRequestStatusEnum;
use App\Enums\Company\Scoring\ScoringRequestTypeEnum;
use App\Models\Company\ScoringRequest;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class SingleRoyxatCreditHistoryGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private array $_structure = [
        'id',
        'ext_id',
        'status',
        'type',
        'client_code',
        'customer_id',
        'card_id',
        'created_at',
        'updated_at',
        'deleted_at',
        'request' => [
            'passport_id',
            'pinfl',
        ],
        'response' => [
            'ok',
            'error_code',
            'description',
            'result' => [
                'borrower' => [
                    'full_name',
                    'passport_id',
                    'pinfl',
                    'birth_date',
                ],
                'loans' => [
                    '*' => [
                        'id',
                        'amount',
                        'contract_date',
                        'max_overdue_days',
                        'total_overdue_days',
                        'total_overdue_amount',
                        'status',
                    ],
                ],
            ],
        ],
    ];

    private ScoringRequest $_scoringRequest;

    protected function setUp(): void
    {
        parent::setUp();

        ScoringRequest::query()->forceDelete();
        $this->_scoringRequest = ScoringRequest::factory()->create(['type' => ScoringRequestTypeEnum::ROYXAT_CREDIT_HISTORY, 'status' => ScoringRequestStatusEnum::SUCCESS]);
    }

    #[Test]
    public function successSingleInpsHistoryRequest()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'), $this->buildGraphQLQuery(['id' => $this->_scoringRequest->id])
            );

        $this->assertSuccessSingleRoyxatCreditHistoryResponse($response);
    }

    #[Test]
    public function errorValidation()
    {
        // non exist scoring request
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'), $this->buildGraphQLQuery(['id' => 999999999])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'id']), $response->json('errors.0.extensions.validation.id.0'));

        // scoring request's status not success
        $this->_scoringRequest = ScoringRequest::factory()->create(['type' => ScoringRequestTypeEnum::ROYXAT_CREDIT_HISTORY, 'status' => ScoringRequestStatusEnum::WAITING]);
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'), $this->buildGraphQLQuery(['id' => $this->_scoringRequest->id])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'id']), $response->json('errors.0.extensions.validation.id.0'));

        // // scoring request's type not royxat
        // $this->_scoringRequest = ScoringRequest::factory()->create(['type' => ScoringRequestTypeEnum::KATM_INPS_HISTORY, 'status' => ScoringRequestStatusEnum::SUCCESS]);
        // $response = $this
        //     ->actingAs($this->createAdminUser())
        //     ->postJson(
        //         route('graphql.scoring'), $this->buildGraphQLQuery(['id' => $this->_scoringRequest->id])
        //     );
        // $response->assertOk();
        // $this->assertArrayHasKey('errors', $response->json());
        // $this->assertEquals('validation', $response->json('errors.0.message'));
        // $this->assertEquals(trans('validation.exists', ['attribute' => 'id']), $response->json('errors.0.extensions.validation.id.0'));

        // deleted
        $this->_scoringRequest->delete();
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'), $this->buildGraphQLQuery(['id' => $this->_scoringRequest->id])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'id']), $response->json('errors.0.extensions.validation.id.0'));
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $query['query'] = '
            query SingleRoyxatCreditHistory($id: Int!) {
            singleRoyxatCreditHistory(id: $id) {' . $this->convertIntoQuery($this->_structure) . '}
        }';

        return $query;
    }

    private function assertSuccessSingleRoyxatCreditHistoryResponse(TestResponse $response): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure(['data' => ['singleRoyxatCreditHistory' => $this->_structure]]);

        $this->assertEquals($this->_scoringRequest->id, $response->json('data.singleRoyxatCreditHistory.id'));
        $this->assertEquals($this->_scoringRequest->ext_id, $response->json('data.singleRoyxatCreditHistory.ext_id'));
        $this->assertEquals($this->_scoringRequest->status->value, $response->json('data.singleRoyxatCreditHistory.status'));
        $this->assertEquals($this->_scoringRequest->type->value, $response->json('data.singleRoyxatCreditHistory.type'));
        $this->assertEquals($this->_scoringRequest->client_code->value, $response->json('data.singleRoyxatCreditHistory.client_code'));
        $this->assertEquals($this->_scoringRequest->customer_id, $response->json('data.singleRoyxatCreditHistory.customer_id'));
        $this->assertEquals($this->_scoringRequest->card_id, $response->json('data.singleRoyxatCreditHistory.card_id'));
        $this->assertEquals($this->_scoringRequest->created_at->timestamp, $response->json('data.singleRoyxatCreditHistory.created_at'));
        $this->assertEquals($this->_scoringRequest->updated_at->timestamp, $response->json('data.singleRoyxatCreditHistory.updated_at'));
        $this->assertEquals($this->_scoringRequest->deleted_at?->timestamp, $response->json('data.singleRoyxatCreditHistory.deleted_at'));

        // request
        $this->assertEquals($this->_scoringRequest->request->passport_id, $response->json('data.singleRoyxatCreditHistory.request.passport_id'));
        $this->assertEquals($this->_scoringRequest->request->pinfl, $response->json('data.singleRoyxatCreditHistory.request.pinfl'));

        // response
        /** @var RoyxatCreditHistoryResponseDTO $scoringRequestResponse */
        $scoringRequestResponse = $this->_scoringRequest->response;
        $this->assertTrue($scoringRequestResponse instanceof RoyxatCreditHistoryResponseDTO);
        $this->assertEquals($scoringRequestResponse->ok, $response->json('data.singleRoyxatCreditHistory.response.ok'));
        $this->assertEquals($scoringRequestResponse->error_code, $response->json('data.singleRoyxatCreditHistory.response.error_code'));
        $this->assertEquals($scoringRequestResponse->description, $response->json('data.singleRoyxatCreditHistory.response.description'));

        $this->assertEquals($scoringRequestResponse->result->borrower->full_name, $response->json('data.singleRoyxatCreditHistory.response.result.borrower.full_name'));
        $this->assertEquals($scoringRequestResponse->result->borrower->passport_id, $response->json('data.singleRoyxatCreditHistory.response.result.borrower.passport_id'));
        $this->assertEquals($scoringRequestResponse->result->borrower->pinfl, $response->json('data.singleRoyxatCreditHistory.response.result.borrower.pinfl'));
        $this->assertEquals($scoringRequestResponse->result->borrower->birth_date, $response->json('data.singleRoyxatCreditHistory.response.result.borrower.birth_date'));

        /** @var RoyxatCreditHistoryLoanDTO $loan */
        $loan = $scoringRequestResponse->result->loans[0];
        $this->assertTrue($loan instanceof RoyxatCreditHistoryLoanDTO);
        $this->assertEquals($loan->id, $response->json('data.singleRoyxatCreditHistory.response.result.loans.0.id'));
        $this->assertEquals($loan->amount / 100, $response->json('data.singleRoyxatCreditHistory.response.result.loans.0.amount'));
        $this->assertEquals($loan->contract_date, $response->json('data.singleRoyxatCreditHistory.response.result.loans.0.contract_date'));
        $this->assertEquals($loan->max_overdue_days, $response->json('data.singleRoyxatCreditHistory.response.result.loans.0.max_overdue_days'));
        $this->assertEquals($loan->total_overdue_days, $response->json('data.singleRoyxatCreditHistory.response.result.loans.0.total_overdue_days'));
        $this->assertEquals($loan->total_overdue_amount, $response->json('data.singleRoyxatCreditHistory.response.result.loans.0.total_overdue_amount'));
        $this->assertEquals($loan->status, $response->json('data.singleRoyxatCreditHistory.response.result.loans.0.status'));
    }
}
