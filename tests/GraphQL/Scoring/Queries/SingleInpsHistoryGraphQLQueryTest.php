<?php

namespace Tests\GraphQL\Scoring\Queries;

use App\DTOs\Scoring\Katm\InpsHistory\KatmIncomeDTO;
use App\DTOs\Scoring\Katm\InpsHistory\KatmIncomesDTO;
use App\DTOs\Scoring\Katm\InpsHistory\KatmIncomesPeriodDTO;
use App\DTOs\Scoring\Katm\InpsHistory\KatmInpsHistoryResponseDTO;
use App\DTOs\Scoring\Katm\InpsHistory\KatmPresenceReportDTO;
use App\DTOs\Scoring\Katm\InpsHistory\KatmPresenceReportsDTO;
use App\DTOs\Scoring\Katm\KatmClientDTO;
use App\DTOs\Scoring\Katm\KatmCreateRequestDTO;
use App\Enums\Company\Scoring\ScoringRequestStatusEnum;
use App\Enums\Company\Scoring\ScoringRequestTypeEnum;
use App\Models\Company\ScoringRequest;
use Illuminate\Support\Carbon;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Spatie\LaravelData\DataCollection;
use Tests\GraphQL\GraphQLTestCase;

class SingleInpsHistoryGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private ScoringRequest $_scoring_request;

    protected function setUp(): void
    {
        parent::setUp();
        ScoringRequest::query()->forceDelete();
        $this->_scoring_request = ScoringRequest::factory()->create(
            [
                'type' => ScoringRequestTypeEnum::KATM_INPS_HISTORY,
                'status' => ScoringRequestStatusEnum::SUCCESS,
                'request' => new KatmCreateRequestDTO('123123123123', 'AA', '1234567', Carbon::createFromDate('15-06-2016'), 'Akmal', 'Baxodirov', 'Azamat', '998882019909', '998882019909', Carbon::createFromDate('09-06-1999'), 13, 114),
                'response' => new KatmInpsHistoryResponseDTO(
                    new KatmClientDTO('fake', '2005-08-10', 'M', 'Laplandiya viloyati katta shaxri', '998812332323', 'Резидент', '12345678912345', '12345678912345', 'СРЕДНИЙ', '2023-09-30 17:54', 'Биометрический паспорт гражданина Республики Узбекистан', 'AA', '1234567', '2022-01-12', '?', '?', '?'),
                    new KatmIncomesPeriodDTO('2023-09-01', '2023-09-30', '0'),
                    new KatmPresenceReportsDTO(
                        new DataCollection(KatmPresenceReportDTO::class, [
                            [
                                'num' => '1',
                                'report_name' => 'Скоринг',
                                'presence' => 'da',
                            ],
                        ])
                    ),
                    new KatmIncomesDTO(
                        new DataCollection(
                            KatmIncomeDTO::class,
                            [
                                [
                                    'num' => '1',
                                    'period' => '2023-07',
                                    'orgname' => '"VORTEX SOLAR" MCHJ',
                                    'org_inn' => '310321569',
                                    'income_summa' => '6001600.00',
                                    'inps_summa' => '6820.00',
                                    'send_date' => '2022-10-01',
                                ],
                            ]
                        )
                    )
                ),
            ]
        );
        $this->mockUrl('https://api.abrand.uz/partner/katm/get-request*', json_decode(file_get_contents(storage_path('../JSON-for-testing/credit-history-create-response.json')), true));

    }

    #[Test]
    public function successSingleInpsHistoryRequest()
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
    public function errorSingleInpsHistoryRequest()
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
        singleInpsHistory(id: $id) {
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
                passport_series
                passport_number
                passport_given_date
                first_name
                last_name
                middle_name
                phone
                extra_phone
                birth_date
                region_id
                district_id
            }
             response {
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
                  incomes_period {
                    incomes_period_begin
                    incomes_period_end
                    incomes_all_summa
                  }
                  presence_reports {
                    presence_report {
                        num
                        report_name
                        presence
                    }
                  }
                  incomes {
                    income {
                      num
                      period
                      orgname
                      org_inn
                      income_summa
                      inps_summa
                      send_date
                    }
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
                'singleInpsHistory' => [
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
                        'passport_series',
                        'passport_number',
                        'passport_given_date',
                        'first_name',
                        'last_name',
                        'middle_name',
                        'phone',
                        'extra_phone',
                        'birth_date',
                        'region_id',
                        'district_id',
                    ],
                    'response' => [
                        'client' => [
                            'name',
                            'birth_date',
                            'gender',
                            'address',
                            'resident',
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
                        'incomes_period' => [
                            'incomes_period_begin',
                            'incomes_period_end',
                            'incomes_all_summa',
                        ],
                        'presence_reports' => [
                            'presence_report' => [
                                '*' => [
                                    'num',
                                    'report_name',
                                    'presence',
                                ],
                            ],
                        ],
                        'incomes' => [
                            'income' => [
                                '*' => [
                                    'num',
                                    'period',
                                    'orgname',
                                    'org_inn',
                                    'income_summa',
                                    'inps_summa',
                                    'send_date',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]
        );
        $scoring = ScoringRequest::query()->find($response->json('data.singleInpsHistory.id'));
        $this->assertArrayHasKey('id', $response->json('data.singleInpsHistory'));
        $this->assertEquals($scoring->id, $response->json('data.singleInpsHistory.id'));
    }
}
