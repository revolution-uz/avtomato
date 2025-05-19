<?php

namespace Tests\GraphQL\Scoring\Queries;

use App\DTOs\Scoring\Katm\CreditHistory\KatmCreditHistoryAdditionalInfoDTO;
use App\DTOs\Scoring\Katm\CreditHistory\KatmCreditHistoryDebtDTO;
use App\DTOs\Scoring\Katm\CreditHistory\KatmCreditHistoryResponseDTO;
use App\DTOs\Scoring\Katm\CreditHistory\KatmCreditHistoryScoreDTO;
use App\DTOs\Scoring\Katm\KatmClientDTO;
use App\DTOs\Scoring\Katm\KatmCreateRequestDTO;
use App\Enums\Company\Scoring\ScoringRequestStatusEnum;
use App\Enums\Company\Scoring\ScoringRequestTypeEnum;
use App\Models\Company\ScoringRequest;
use Illuminate\Support\Carbon;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class SingleCreditHistoryGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private ScoringRequest $_scoring_request;

    protected function setUp(): void
    {
        parent::setUp();
        ScoringRequest::query()->forceDelete();
        $this->_scoring_request = ScoringRequest::factory()->create(
            [
                'type' => ScoringRequestTypeEnum::KATM_CREDIT_HISTORY,
                'status' => ScoringRequestStatusEnum::SUCCESS,
                'request' => new KatmCreateRequestDTO('30906997180039', 'AB', '4143286', Carbon::createFromDate('15-06-2016'), 'Akmal', 'Baxodirov', 'Azamat', '998882019909', '998882019909', Carbon::createFromDate('09-06-1999'), 13, 114),
                'response' => new KatmCreditHistoryResponseDTO(
                    new KatmCreditHistoryAdditionalInfoDTO(
                        new KatmCreditHistoryDebtDTO(1.1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24),
                        new KatmCreditHistoryDebtDTO(1.1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24),
                        22.3,
                        []
                    ),
                    new KatmCreditHistoryScoreDTO(273, 'C3', 'СРЕДНИЙ', '2023-09-30 17:54', '1.6'),
                    new KatmClientDTO('Yura', '2005-08-10', 'M', 'Xorazm viloyati Urganch shaxri', '998882223123', 'Резидент', '12345678912345', '12345678912345', 'СРЕДНИЙ', '2023-09-30 17:54', 'Биометрический паспорт гражданина Республики Узбекистан', 'AA', '1234567', '2022-01-12', '?', '?', '?'),
                ),
            ]);
        $this->mockUrl('https://api.abrand.uz/partner/katm/get-request*', json_decode(file_get_contents(storage_path('../JSON-for-testing/credit-history-create-response.json')), true));

    }

    #[Test]
    public function successSingleCreditHistoryRequest()
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
    public function errorSingleCreditHistoryRequest()
    {
        // not found
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery(12345)
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
        singleCreditHistory(id: $id) {
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
                  additional_info {
                        current_debt {
                          all_delays_qty
                          all_delays_sum
                          contracts_wo_delays_qty
                          contracts_wo_delays_sum
                          expired_percent_qty
                          expired_percent_sum
                          revised_contracts_qty
                          revised_contracts_sum
                          delays_less_30_qty
                          delays_less_30_sum
                          switched_month_qty
                          switched_month_sum
                          delays_less_60_qty
                          delays_less_60_sum
                          delays_less_90_qty
                          delays_less_90_sum
                          delays_more_90_qty
                          delays_more_90_sum
                          delays_in_court_qty
                          delays_in_court_sum
                          delays_decommissioned_qty
                          delays_decommissioned_sum
                          delays_decommissioned_pcnt_qty
                          delays_decommissioned_pcnt_sum
                        }
                        closed_debt {
                              all_delays_qty
                              all_delays_sum
                              contracts_wo_delays_qty
                              contracts_wo_delays_sum
                              expired_percent_qty
                              expired_percent_sum
                              revised_contracts_qty
                              revised_contracts_sum
                              delays_less_30_qty
                              delays_less_30_sum
                              switched_month_qty
                              switched_month_sum
                              delays_less_60_qty
                              delays_less_60_sum
                              delays_less_90_qty
                              delays_less_90_sum
                              delays_more_90_qty
                              delays_more_90_sum
                              delays_in_court_qty
                              delays_in_court_sum
                              delays_decommissioned_qty
                              delays_decommissioned_sum
                              delays_decommissioned_pcnt_qty
                              delays_decommissioned_pcnt_sum
                        }
                        average_monthly_payment
                        notifications
                  }
                  score {
                    score_point
                    score_class
                    score_level
                    score_date
                    score_version
                  }
                  client {
                        name
                        birth_date
                        gender
                        address
                        resident
                        pinfl
                        inn
                        document_type
                        document_serial
                        document_number
                        document_date
                        document_type_id
                        subject_id
                        credit_start_date
                  }
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
                'singleCreditHistory' => [
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
                        'additional_info' => [
                            'current_debt' => [
                                'all_delays_qty',
                                'all_delays_sum',
                                'contracts_wo_delays_qty',
                                'contracts_wo_delays_sum',
                                'expired_percent_qty',
                                'expired_percent_sum',
                                'revised_contracts_qty',
                                'revised_contracts_sum',
                                'delays_less_30_qty',
                                'delays_less_30_sum',
                                'switched_month_qty',
                                'switched_month_sum',
                                'delays_less_60_qty',
                                'delays_less_60_sum',
                                'delays_less_90_qty',
                                'delays_less_90_sum',
                                'delays_more_90_qty',
                                'delays_more_90_sum',
                                'delays_in_court_qty',
                                'delays_in_court_sum',
                                'delays_decommissioned_qty',
                                'delays_decommissioned_sum',
                                'delays_decommissioned_pcnt_qty',
                                'delays_decommissioned_pcnt_sum',
                            ],
                            'closed_debt' => [
                                'all_delays_qty',
                                'all_delays_sum',
                                'contracts_wo_delays_qty',
                                'contracts_wo_delays_sum',
                                'expired_percent_qty',
                                'expired_percent_sum',
                                'revised_contracts_qty',
                                'revised_contracts_sum',
                                'delays_less_30_qty',
                                'delays_less_30_sum',
                                'switched_month_qty',
                                'switched_month_sum',
                                'delays_less_60_qty',
                                'delays_less_60_sum',
                                'delays_less_90_qty',
                                'delays_less_90_sum',
                                'delays_more_90_qty',
                                'delays_more_90_sum',
                                'delays_in_court_qty',
                                'delays_in_court_sum',
                                'delays_decommissioned_qty',
                                'delays_decommissioned_sum',
                                'delays_decommissioned_pcnt_qty',
                                'delays_decommissioned_pcnt_sum',
                            ],
                            'average_monthly_payment',
                            'notifications',
                        ],
                        'score' => [
                            'score_point',
                            'score_class',
                            'score_level',
                            'score_date',
                            'score_version',
                        ],
                        'client' => [
                            'name',
                            'birth_date',
                            'gender',
                            'resident',
                            'address',
                            'pinfl',
                            'inn',
                            'document_type',
                            'document_serial',
                            'document_number',
                            'document_date',
                            'document_type_id',
                            'subject_id',
                            'credit_start_date',
                        ],
                    ],
                ],
            ],
        ]);
        $scoring = ScoringRequest::query()->find($response->json('data.singleCreditHistory.id'));
        $this->assertArrayHasKey('id', $response->json('data.singleCreditHistory'));
        $this->assertEquals($scoring->id, $response->json('data.singleCreditHistory.id'));
    }
}
