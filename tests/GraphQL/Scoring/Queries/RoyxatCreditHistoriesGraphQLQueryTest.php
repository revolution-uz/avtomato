<?php

namespace Tests\GraphQL\Scoring\Queries;

use App\Enums\Company\Scoring\ScoringRequestTypeEnum;
use App\Models\Company\ScoringRequest;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class RoyxatCreditHistoriesGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private int $_totalCreditsCount = 59;

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

    protected function setUp(): void
    {
        parent::setUp();

        ScoringRequest::query()->forceDelete();
        ScoringRequest::factory()->count($this->_totalCreditsCount)->create(['type' => ScoringRequestTypeEnum::ROYXAT_CREDIT_HISTORY]);
    }

    #[Test]
    public function successWithoutArguments(): void
    {
        $response = $this->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'), $this->buildGraphQLQuery()
            );
        $this->assertSuccessRoyxatCreditHistoriesResponse($response);
    }

    #[Test]
    public function successPagination()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'), $this->buildGraphQLQuery(['page' => 4])
            );
        $this->assertSuccessRoyxatCreditHistoriesResponse($response, page: 4);
    }

    #[Test]
    public function successPerPage()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'), $this->buildGraphQLQuery(['per_page' => 25])
            );
        $this->assertSuccessRoyxatCreditHistoriesResponse($response, perPage: 25);
    }

    #[Test]
    public function successSortAscending()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'), $this->buildGraphQLQuery(['sort' => 'id'])
            );
        $this->assertSuccessRoyxatCreditHistoriesResponse($response);
        $scoringIds = ScoringRequest::query()->orderBy('id')->limit(10)->pluck('id')->toArray();
        $this->assertEquals($scoringIds, $response->json('data.royxatCreditHistories.data.*.id'));
    }

    #[Test]
    public function successSortDescending()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'), $this->buildGraphQLQuery(['sort' => '-id'])
            );
        $this->assertSuccessRoyxatCreditHistoriesResponse($response);
        $scoringIds = ScoringRequest::query()->orderByDesc('id')->limit(10)->pluck('id')->toArray();
        $this->assertEquals($scoringIds, $response->json('data.royxatCreditHistories.data.*.id'));
    }

    #[Test]
    public function successSearchPensionInfos()
    {
        $scoring = ScoringRequest::query()->first();

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'), $this->buildGraphQLQuery(['search' => $scoring->request->pinfl])
            );

        $this->assertSuccessRoyxatCreditHistoriesResponse($response, total: 1);
        $this->assertEquals($scoring->request->pinfl, $response->json('data.royxatCreditHistories.data.0.request.pinfl'));
    }

    #[Test]
    public function errorPage()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'), $this->buildGraphQLQuery(['page' => 'asnjfasnjfasn'])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals(
            'Variable "$page" got invalid value "asnjfasnjfasn"; Int cannot represent non-integer value: "asnjfasnjfasn"',
            $response->json('errors.0.message')
        );

        // minimum validation test
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'), $this->buildGraphQLQuery(['page' => 0])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(
            trans('validation.min.numeric', ['attribute' => 'page', 'min' => 1]),
            $response->json('errors.0.extensions.validation.page.0')
        );
    }

    #[Test]
    public function errorPerPage()
    {
        // graphql type test
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'), $this->buildGraphQLQuery(['per_page' => 'asnjfasnjfasn'])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals(
            'Variable "$per_page" got invalid value "asnjfasnjfasn"; Int cannot represent non-integer value: "asnjfasnjfasn"',
            $response->json('errors.0.message')
        );

        // minimum validation test
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'), $this->buildGraphQLQuery(['per_page' => 0])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(
            trans('validation.min.numeric', ['attribute' => 'per page', 'min' => 5]),
            $response->json('errors.0.extensions.validation.per_page.0')
        );

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
        $this->assertEquals(
            trans('validation.max.numeric', ['attribute' => 'per page', 'max' => 100]),
            $response->json('errors.0.extensions.validation.per_page.0')
        );
    }

    #[Test]
    public function errorSort()
    {
        // graphql type test
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'), $this->buildGraphQLQuery(['sort' => 111])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals(
            'Variable "$sort" got invalid value 111; String cannot represent a non string value: 111',
            $response->json('errors.0.message')
        );

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.scoring'),
                $this->buildGraphQLQuery(['sort' => 'non-exist-column'])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(
            trans('validation.regex', ['attribute' => 'sort']),
            $response->json('errors.0.extensions.validation.sort.0')
        );
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $query['query'] = '
            query RoyxatCreditHistories(
                $sort: String
                $search: String
                $page: Int
                $per_page: Int
            ) {
                royxatCreditHistories(
                    sort: $sort
                    search: $search
                    page: $page
                    per_page: $per_page
                ) {
                    total
                    per_page
                    current_page
                    from
                    to
                    last_page
                    has_more_pages
                    data {' . $this->convertIntoQuery($this->_structure) . '}
                }
            }';

        return $query;
    }

    private function assertSuccessRoyxatCreditHistoriesResponse(TestResponse $response, $total = null, $perPage = null, $page = null): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'royxatCreditHistories' => [
                    'total',
                    'per_page',
                    'current_page',
                    'from',
                    'to',
                    'last_page',
                    'has_more_pages',
                    'data' => [
                        '*' => $this->_structure,
                    ],
                ],
            ],
        ]);
        $totalCount = $total ?? $this->_totalCreditsCount;
        $currentPage = $page ?? 1;
        $currentPerPage = $perPage ?? 10;
        $this->assertEquals($totalCount, $response->json('data.royxatCreditHistories.total'));
        $this->assertEquals($currentPerPage, $response->json('data.royxatCreditHistories.per_page'));
        $this->assertEquals($currentPage, $response->json('data.royxatCreditHistories.current_page'));
        $this->assertEquals(($currentPage - 1) * ($currentPerPage) + 1, $response->json('data.royxatCreditHistories.from'));
        $this->assertEquals(min($totalCount, $currentPage * $currentPerPage), $response->json('data.royxatCreditHistories.to'));
        $this->assertEquals(ceil($totalCount / $currentPerPage) > $currentPage, $response->json('data.royxatCreditHistories.has_more_pages'));
        $this->assertEquals(ceil($totalCount / $currentPerPage), $response->json('data.royxatCreditHistories.last_page'));
        $this->assertCount(min($totalCount - ($currentPage - 1) * $currentPerPage, $currentPerPage), $response->json('data.royxatCreditHistories.data'));
    }
}
