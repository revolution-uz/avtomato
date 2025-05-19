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
use App\Enums\Company\Scoring\ScoringRequestTypeEnum;
use App\Models\Company\ScoringRequest;
use Illuminate\Support\Carbon;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Spatie\LaravelData\DataCollection;
use Tests\GraphQL\GraphQLTestCase;

class InpsHistoriesGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private int $_totalInpsCount = 50;

    protected function setUp(): void
    {
        parent::setUp();
        ScoringRequest::query()->forceDelete();
        ScoringRequest::factory()->count($this->_totalInpsCount)->create(
            [
                'type' => ScoringRequestTypeEnum::KATM_INPS_HISTORY,
                'request' => new KatmCreateRequestDTO('30906997180039', 'AB', '4143286', Carbon::createFromDate('15-06-2016'), 'Akmal', 'Baxodirov', 'Azamat', '998882019909', '998882019909', Carbon::createFromDate('09-06-1999'), 13, 114),
                'response' => new KatmInpsHistoryResponseDTO(
                    new KatmClientDTO('Yura', '2005-08-10', 'M', 'Xorazm viloyati Urganch shaxri', '998882223123', 'Резидент', '12345678912345', '12345678912345', 'СРЕДНИЙ', '2023-09-30 17:54', 'Биометрический паспорт гражданина Республики Узбекистан', 'AA', '1234567', '2022-01-12', '?', '?', '?'),
                    new KatmIncomesPeriodDTO('2022-10-01', '2023-10-01', '0'),
                    new KatmPresenceReportsDTO(
                        new DataCollection(
                            KatmPresenceReportDTO::class,
                            [
                                new KatmPresenceReportDTO(1, '23', 'asd'),
                                new KatmPresenceReportDTO(1, '23', 'asd'),
                            ]
                        )
                    ),
                    new KatmIncomesDTO(
                        new DataCollection(
                            KatmIncomeDTO::class,
                            [
                                new KatmIncomeDTO('1', '2023-07', '"VORTEX SOLAR" MCHJ', '310321569', '6001600.00', '6820.00', '2022-10-01'),
                                new KatmIncomeDTO('1', '2023-07', '"VORTEX SOLAR" MCHJ', '310321569', '6001600.00', '6820.00', '2022-10-01'),
                            ]
                        )
                    )

                ),
            ]);
        $this->mockUrl('https://api.abrand.uz/partner/katm/get-request*', json_decode(file_get_contents(storage_path('../JSON-for-testing/credit-history-create-response.json')), true));

    }

    #[Test]
    public function successWithoutArguments(): void
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery()
            );
        $response->assertOk();
    }

    #[Test]
    public function successPagination()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery(['page' => 4])
            );
        $this->assertSuccessInpsResponse($response, page: 4);
    }

    #[Test]
    public function successPerPage()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery(['per_page' => 25])
            );
        $this->assertSuccessInpsResponse($response, perPage: 25);
    }

    #[Test]
    public function successSortAscending()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery(['sort' => 'id'])
            );

        $this->assertSuccessInpsResponse($response);
        $scoringIds = ScoringRequest::query()->orderBy('id')->limit(10)->pluck('id')->toArray();
        $this->assertEquals($scoringIds, $response->json('data.inpsHistories.data.*.id'));
    }

    #[Test]
    public function successSortDescending()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery(['sort' => '-id'])
            );

        $this->assertSuccessInpsResponse($response);
        $scoringIds = ScoringRequest::query()->orderByDesc('id')->limit(10)->pluck('id')->toArray();
        $this->assertEquals($scoringIds, $response->json('data.inpsHistories.data.*.id'));
    }

    #[Test]
    public function successSearch()
    {
        $scoring = ScoringRequest::query()->first();
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery(['search' => $scoring->request->pinfl])
            );

        $this->assertSuccessInpsResponse($response, total: $this->_totalInpsCount);
        $this->assertEquals($scoring->request->pinfl, $response->json('data.inpsHistories.data.0.request.pinfl'));
    }

    #[Test]
    public function errorPage()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery(['page' => 'asnjfasnjfasn'])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('Variable "$page" of type "String!" used in position expecting type "Int".', $response->json('errors.0.message'));

        // minimum validation test
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery(['page' => 0])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.min.numeric', ['attribute' => 'page', 'min' => 1]), $response->json('errors.0.extensions.validation.page.0'));
    }

    #[Test]
    public function errorPerPage()
    {
        // graphql type test
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery(['per_page' => 'asnjfasnjfasn'])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('Variable "$per_page" of type "String!" used in position expecting type "Int".', $response->json('errors.0.message'));

        // minimum validation test
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery(['per_page' => 0])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.min.numeric', ['attribute' => 'per page', 'min' => 5]), $response->json('errors.0.extensions.validation.per_page.0'));

        // maximum validation test
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery(['per_page' => 101])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.max.numeric', ['attribute' => 'per page', 'max' => 100]), $response->json('errors.0.extensions.validation.per_page.0'));
    }

    #[Test]
    public function errorSort()
    {
        // graphql type test
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery(['sort' => 111])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('Variable "$sort" of type "Int!" used in position expecting type "String".', $response->json('errors.0.message'));

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery(['sort' => 'non-exist-column'])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.regex', ['attribute' => 'sort']), $response->json('errors.0.extensions.validation.sort.0'));
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $variableArgs = implode(', ', array_map(fn ($variableName) => "{$variableName}: \${$variableName}", array_keys($variables)));
        $variableTypes = implode(', ', array_map(fn ($variableName) => "\${$variableName}: " . $this->getGraphQLType($variables[$variableName]), array_keys($variables)));
        $query['query'] = '
            query inpsHistories' . ($variableTypes ? "({$variableTypes})" : '') . ' {
                inpsHistories' . ($variableArgs ? "({$variableArgs})" : '') . ' {
                    current_page
                    from
                    has_more_pages
                    last_page
                    per_page
                    to
                    total
                    data {
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
                        }
                    }
                }
            }
        ';

        return $query;
    }

    private function assertSuccessInpsResponse(TestResponse $response, $total = null, $perPage = null, $page = null): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'inpsHistories' => [
                    'current_page',
                    'from',
                    'has_more_pages',
                    'last_page',
                    'per_page',
                    'to',
                    'total',
                    'data' => [
                        '*' => [
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
                            ],
                        ],
                    ],
                ],
            ],
        ]);
        $totalCount = $total ?? $this->_totalInpsCount;
        $currentPage = $page ?? 1;
        $currentPerPage = $perPage ?? 10;
        $this->assertEquals($totalCount, $response->json('data.inpsHistories.total'));
        $this->assertEquals($currentPerPage, $response->json('data.inpsHistories.per_page'));
        $this->assertEquals($currentPage, $response->json('data.inpsHistories.current_page'));
        $this->assertEquals(($currentPage - 1) * ($currentPerPage) + 1, $response->json('data.inpsHistories.from'));
        $this->assertEquals(min($totalCount, $currentPage * $currentPerPage), $response->json('data.inpsHistories.to'));
        $this->assertEquals(ceil($totalCount / $currentPerPage) > $currentPage, $response->json('data.inpsHistories.has_more_pages'));
        $this->assertEquals(ceil($totalCount / $currentPerPage), $response->json('data.inpsHistories.last_page'));
        $this->assertCount(min($totalCount - ($currentPage - 1) * $currentPerPage, $currentPerPage), $response->json('data.inpsHistories.data'));
    }
}
