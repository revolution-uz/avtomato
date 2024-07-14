<?php

namespace Tests\GraphQL\Scoring\Queries;

use App\DTOs\Scoring\Katm\KatmCreateRequestDTO;
use App\DTOs\Scoring\Katm\PensionInfo\KatmPensionDataDTO;
use App\DTOs\Scoring\Katm\PensionInfo\KatmPensionInfoResponseDTO;
use App\DTOs\Scoring\Katm\PensionInfo\KatmPensionRowDTO;
use App\DTOs\Scoring\Katm\PensionInfo\KatmPensionSizePensionDTO;
use App\Enums\Company\Scoring\ScoringRequestTypeEnum;
use App\Models\Company\ScoringRequest;
use Illuminate\Support\Carbon;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Spatie\LaravelData\DataCollection;
use Tests\GraphQL\GraphQLTestCase;

class PensionInfosGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private int $_totalPensionCount = 59;

    protected function setUp(): void
    {
        parent::setUp();

        ScoringRequest::query()->forceDelete();
        ScoringRequest::factory()->count($this->_totalPensionCount)->create(
            [
                'type' => ScoringRequestTypeEnum::KATM_PENSION_INFO,
                'request' => new KatmCreateRequestDTO('30906997180039', 'AB', '4143286', Carbon::createFromDate('15-06-2016'), 'Akmal', 'Baxodirov', 'Azamat', '998882019909', '998882019909', Carbon::createFromDate('09-06-1999'), 13, 114),
                'response' => new KatmPensionInfoResponseDTO(
                    '1',
                    new KatmPensionSizePensionDTO(
                        new KatmPensionDataDTO('Muhiba', 'BOBOATO1 12 12 12', '1926720.03', 'Qamarovna', 'AA1737639', '01', 'Ergasheva', '4 yil 0 oy 0 kun', '1', '8', '42405621120018', '24.05.2016'),
                        new DataCollection(
                            KatmPensionRowDTO::class,
                            [
                                new KatmPensionRowDTO('2022/10', 'Oktyabr', 1926720.03),
                                new KatmPensionRowDTO('2022/10', 'Oktyabr', 1926720.03),
                            ]
                        ),
                    ),
                    'Неявляется получателем пенсии (пособия)'

                ),
            ]
        );
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
        $this->assertSuccessPensionResponse($response, page: 4);
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
        $this->assertSuccessPensionResponse($response, perPage: 25);
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

        $this->assertSuccessPensionResponse($response);
        $scoringIds = ScoringRequest::query()->orderBy('id')->limit(10)->pluck('id')->toArray();
        $this->assertEquals($scoringIds, $response->json('data.pensionInfos.data.*.id'));
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

        $this->assertSuccessPensionResponse($response);
        $scoringIds = ScoringRequest::query()->orderByDesc('id')->limit(10)->pluck('id')->toArray();
        $this->assertEquals($scoringIds, $response->json('data.pensionInfos.data.*.id'));
    }

    #[Test]
    public function successSearchPensionInfos()
    {
        $scoring = ScoringRequest::query()->first();
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery(['search' => $scoring->request->pinfl])
            );

        $this->assertSuccessPensionResponse($response, total: $this->_totalPensionCount);
        $this->assertEquals($scoring->request->pinfl, $response->json('data.pensionInfos.data.0.request.pinfl'));
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
            query pensionInfos' . ($variableTypes ? "({$variableTypes})" : '') . ' {
                pensionInfos' . ($variableArgs ? "({$variableArgs})" : '') . ' {
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

    private function assertSuccessPensionResponse(TestResponse $response, $total = null, $perPage = null, $page = null): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'pensionInfos' => [
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
        $totalCount = $total ?? $this->_totalPensionCount;
        $currentPage = $page ?? 1;
        $currentPerPage = $perPage ?? 10;
        $this->assertEquals($totalCount, $response->json('data.pensionInfos.total'));
        $this->assertEquals($currentPerPage, $response->json('data.pensionInfos.per_page'));
        $this->assertEquals($currentPage, $response->json('data.pensionInfos.current_page'));
        $this->assertEquals(($currentPage - 1) * ($currentPerPage) + 1, $response->json('data.pensionInfos.from'));
        $this->assertEquals(min($totalCount, $currentPage * $currentPerPage), $response->json('data.pensionInfos.to'));
        $this->assertEquals(ceil($totalCount / $currentPerPage) > $currentPage, $response->json('data.pensionInfos.has_more_pages'));
        $this->assertEquals(ceil($totalCount / $currentPerPage), $response->json('data.pensionInfos.last_page'));
        $this->assertCount(min($totalCount - ($currentPage - 1) * $currentPerPage, $currentPerPage), $response->json('data.pensionInfos.data'));
    }
}
