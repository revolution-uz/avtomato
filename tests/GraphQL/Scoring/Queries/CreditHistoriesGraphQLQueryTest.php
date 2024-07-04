<?php

namespace Tests\GraphQL\Scoring\Queries;

use App\DTOs\Scoring\Katm\CreditHistory\KatmCreditHistoryAdditionalInfoDTO;
use App\DTOs\Scoring\Katm\CreditHistory\KatmCreditHistoryDebtDTO;
use App\DTOs\Scoring\Katm\CreditHistory\KatmCreditHistoryResponseDTO;
use App\DTOs\Scoring\Katm\CreditHistory\KatmCreditHistoryScoreDTO;
use App\DTOs\Scoring\Katm\KatmClientDTO;
use App\DTOs\Scoring\Katm\KatmCreateRequestDTO;
use App\Enums\Company\Scoring\ScoringRequestTypeEnum;
use App\Models\Company\ScoringRequest;
use Illuminate\Support\Carbon;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class CreditHistoriesGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    protected function setUp(): void
    {
        parent::setUp();
        ScoringRequest::query()->forceDelete();
        ScoringRequest::factory()->count(5)->create(
            [
                'type' => ScoringRequestTypeEnum::KATM_CREDIT_HISTORY,
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
                )
            ]);
    }

    #[Test]
    public function successCreditHistoriesQuery()
    {
        $response = $this->actingAs($this->createAdminUser())->postJson(
            route('graphql.scoring'),
            [
                'query' => '
                    query {
                        creditHistories {
                            data {
                                id
                                card_id
                                client_code
                                created_at
                                updated_at
                                customer_id
                                ext_id
                                status
                                type
                                request {
                                      pinfl
                                      first_name
                                      last_name
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
                        }
                    }
                '
            ]
        );
        $this->assertSuccessCreditHistoriesResponse($response);
    }

    #[Test]
    public function paginationCreditHistoriesQuery()
    {
        $response = $this->actingAs($this->createAdminUser())->postJson(
            route('graphql.scoring'),
            [
                'query' => '
                    query {
                        creditHistories(page: 2, per_page: 5) {
                            current_page
                            from
                            has_more_pages
                            last_page
                            per_page
                            to
                            total
                            data {
                                id
                                card_id
                                client_code
                                created_at
                                updated_at
                                customer_id
                                ext_id
                                status
                                type
                                request {
                                      pinfl
                                      first_name
                                      last_name
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
                        }
                    }
                '
            ]
        );
        $response->assertOk();
        $response->assertJsonPath('data.creditHistories.current_page', 2);
    }

    #[Test]
    public function sortCreditHistoriesQuery()
    {
        $response = $this->actingAs($this->createAdminUser())->postJson(
            route('graphql.scoring'),
            [
                'query' => '
                query {
                    creditHistories(sort: "-created_at") {
                        data {
                            id
                            card_id
                            client_code
                            created_at
                            updated_at
                            customer_id
                            ext_id
                            status
                            type
                            request {
                                  pinfl
                                  first_name
                                  last_name
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
                    }
                }
            '
            ]
        );
        $response->assertOk();

        $createdAtFirst = $response->json('data.creditHistories.data.0.created_at');
        $createdAtSecond = $response->json('data.creditHistories.data.1.created_at');

        $this->assertTrue(strtotime($createdAtFirst) >= strtotime($createdAtSecond));
    }

    private function assertSuccessCreditHistoriesResponse(TestResponse $response): void
    {
        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'creditHistories' => [
                    'data' => [
                        '*' => [
                            'id',
                            'card_id',
                            'client_code',
                            'created_at',
                            'updated_at',
                            'customer_id',
                            'ext_id',
                            'status',
                            'type',
                            'request' => [
                                'pinfl',
                                'first_name',
                                'last_name'
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
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]);
    }
}