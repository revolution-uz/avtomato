<?php

namespace Tests\GraphQL\Scoring\Queries;

use App\DTOs\Scoring\Plum\Mib\MibInfoCreateRequestDTO;
use App\DTOs\Scoring\Plum\Mib\MibInfoResponseDTO;
use App\Enums\Company\Scoring\ScoringRequestTypeEnum;
use App\Models\Company\ScoringRequest;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class DebtScoringRequestGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private int $_totalScoringCount = 50;

    protected function setUp(): void
    {
        parent::setUp();

        ScoringRequest::query()->forceDelete();
        ScoringRequest::factory()->count($this->_totalScoringCount)->create(
            [
                'type' => ScoringRequestTypeEnum::PLUM_MIB_INFO,
                'request' => new MibInfoCreateRequestDTO('21710025140075', '+998938612345'),
                'response' => new MibInfoResponseDTO(1)
            ]
        );
    }

    #[Test]
    public function errorWithoutTypeArgument()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery()
            );
        $this->assertEquals('Field "debtScoringRequests" argument "type" of type "Int!" is required but not provided.', $response->json('errors.0.message'));
    }
    #[Test]
    public function successPagination()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery(['type' => ScoringRequestTypeEnum::PLUM_MIB_INFO->value, 'page' => 4])
            );
        $this->assertSuccessScoringResponse($response, page: 4);
    }

    #[Test]
    public function successPerPage()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery(['type' => ScoringRequestTypeEnum::PLUM_MIB_INFO->value, 'per_page' => 25])
            );
        $this->assertSuccessScoringResponse($response, perPage: 25);
    }

    #[Test]
    public function successSortAscending()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery(['type' => ScoringRequestTypeEnum::PLUM_MIB_INFO->value, 'sort' => 'id'])
            );

        $this->assertSuccessScoringResponse($response);
        $scoringIds = ScoringRequest::query()->orderBy('id')->limit(10)->pluck('id')->toArray();
        $this->assertEquals($scoringIds, $response->json('data.debtScoringRequests.data.*.id'));
    }

    #[Test]
    public function successSortDescending()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery(['type' => ScoringRequestTypeEnum::PLUM_MIB_INFO->value, 'sort' => '-id'])
            );

        $this->assertSuccessScoringResponse($response);
        $scoringIds = ScoringRequest::query()->orderByDesc('id')->limit(10)->pluck('id')->toArray();
        $this->assertEquals($scoringIds, $response->json('data.debtScoringRequests.data.*.id'));
    }

    #[Test]
    public function successSearch()
    {
        $scoring = ScoringRequest::query()->first();
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery(['type' => ScoringRequestTypeEnum::PLUM_MIB_INFO->value, 'search' => "{$scoring->request->phone}",])
            );
        $this->assertSuccessScoringResponse($response);
        $this->assertEquals($scoring->request->phone, $response->json('data.debtScoringRequests.data.0.request.phone'));
    }

    #[Test]
    public function errorPage()
    {
        // graphql type test
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery(['type' => ScoringRequestTypeEnum::PLUM_MIB_INFO->value, 'page' => 'asnjfasnjfasn'])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('Variable "$page" of type "String!" used in position expecting type "Int".', $response->json('errors.0.message'));

        // minimum validation test
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery(['type' => ScoringRequestTypeEnum::PLUM_MIB_INFO->value, 'page' => 0])
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
                $this->buildGraphQLQuery(['type' => ScoringRequestTypeEnum::PLUM_MIB_INFO->value, 'per_page' => 'asnjfasnjfasn'])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('Variable "$per_page" of type "String!" used in position expecting type "Int".', $response->json('errors.0.message'));

        // minimum validation test
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery(['type' => ScoringRequestTypeEnum::PLUM_MIB_INFO->value, 'per_page' => 0])
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
                $this->buildGraphQLQuery(['type' => ScoringRequestTypeEnum::PLUM_MIB_INFO->value, 'per_page' => 101])
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
                $this->buildGraphQLQuery(['type' => ScoringRequestTypeEnum::PLUM_MIB_INFO->value, 'sort' => 111])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('Variable "$sort" of type "Int!" used in position expecting type "String".', $response->json('errors.0.message'));

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery(['type' => ScoringRequestTypeEnum::PLUM_MIB_INFO->value, 'sort' => 'non-exist-column'])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.regex', ['attribute' => 'sort']), $response->json('errors.0.extensions.validation.sort.0'));
    }

    #[Test]
    private function buildGraphQLQuery(array $variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $variableArgs = implode(', ', array_map(fn ($variableName) => "{$variableName}: \${$variableName}", array_keys($variables)));
        $variableTypes = implode(', ', array_map(fn ($variableName) => "\${$variableName}: " . $this->getGraphQLType($variables[$variableName]), array_keys($variables)));
        $query['query'] = '
            query debtScoringRequests' . ($variableTypes ? "({$variableTypes})" : '') . ' {
                debtScoringRequests' . ($variableArgs ? "({$variableArgs})" : '') . ' {
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
                        request {
                                  pinfl
                                  phone
                                }
                        created_at
                        updated_at
                    }
                }
            }
        ';

        return $query;
    }

    #[Test]
    private function assertSuccessScoringResponse(TestResponse $response, $total = null, $perPage = null, $page = null): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'debtScoringRequests' => [
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
                        ],
                    ],
                ],
            ],
        ]);
        $totalCount = $total ?? $this->_totalScoringCount;
        $currentPage = $page ?? 1;
        $currentPerPage = $perPage ?? 10;
        $this->assertEquals($totalCount, $response->json('data.debtScoringRequests.total'));
        $this->assertEquals($currentPerPage, $response->json('data.debtScoringRequests.per_page'));
        $this->assertEquals($currentPage, $response->json('data.debtScoringRequests.current_page'));
        $this->assertEquals(($currentPage - 1) * ($currentPerPage) + 1, $response->json('data.debtScoringRequests.from'));
        $this->assertEquals(min($totalCount, $currentPage * $currentPerPage), $response->json('data.debtScoringRequests.to'));
        $this->assertEquals(ceil($totalCount / $currentPerPage) > $currentPage, $response->json('data.debtScoringRequests.has_more_pages'));
        $this->assertEquals(ceil($totalCount / $currentPerPage), $response->json('data.debtScoringRequests.last_page'));
        $this->assertCount(min($totalCount - ($currentPage - 1) * $currentPerPage, $currentPerPage), $response->json('data.debtScoringRequests.data'));
    }
}
