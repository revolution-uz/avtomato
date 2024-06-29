<?php

namespace Tests\GraphQL\Customer\Queries;

use App\Models\Company\Customer;
use Illuminate\Testing\TestResponse;
use Tests\GraphQL\GraphQLTestCase;

class CustomersGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private int $_totalCustomersCount = 31;

    protected function setUp(): void
    {
        parent::setUp();

        Customer::query()->forceDelete();
        Customer::factory()->count($this->_totalCustomersCount)->create();
    }

    /**
     * @test
     */
    public function successWithoutArguments()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery()
            );

        $this->assertSuccessCustomersResponse($response);
    }

    /**
     * @test
     */
    public function successPagination()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery(['page' => 4])
            );

        $this->assertSuccessCustomersResponse($response, page: 4);
    }

    /**
     * @test
     */
    public function successPerPage()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery(['per_page' => 25])
            );

        $this->assertSuccessCustomersResponse($response, perPage: 25);
    }

    /**
     * @test
     */
    public function successSortAscending()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery(['sort' => 'id'])
            );

        $this->assertSuccessCustomersResponse($response);
        $customerIds = Customer::orderBy('id')->limit(10)->pluck('id')->toArray();
        $this->assertEquals($customerIds, $response->json('data.customers.data.*.id'));
    }

    /**
     * @test
     */
    public function successSortDescending()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery(['sort' => '-id'])
            );

        $this->assertSuccessCustomersResponse($response);
        $customerIds = Customer::orderByDesc('id')->limit(10)->pluck('id')->toArray();
        $this->assertEquals($customerIds, $response->json('data.customers.data.*.id'));
    }

    /**
     * @test
     */
    public function successSearch()
    {
        $customer = Customer::first();
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery(['search' => $customer->pinfl])
            );

        $this->assertSuccessCustomersResponse($response, total: 1);
        $this->assertEquals($customer->id, $response->json('data.customers.data.0.id'));
    }

    /**
     * @test
     */
    public function errorPage()
    {
        // graphql type test
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery(['page' => 'asnjfasnjfasn'])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('Variable "$page" of type "String" used in position expecting type "Int".', $response->json('errors.0.message'));

        // minimum validation test
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery(['page' => 0])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.min.numeric', ['attribute' => 'page', 'min' => 1]), $response->json('errors.0.extensions.validation.page.0'));
    }

    /**
     * @test
     */
    public function errorPerPage()
    {
        // graphql type test
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery(['per_page' => 'asnjfasnjfasn'])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('Variable "$per_page" of type "String" used in position expecting type "Int".', $response->json('errors.0.message'));

        // minimum validation test
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
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
                route('graphql.customer'),
                $this->buildGraphQLQuery(['per_page' => 101])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.max.numeric', ['attribute' => 'per page', 'max' => 100]), $response->json('errors.0.extensions.validation.per_page.0'));
    }

    /**
     * @test
     */
    public function errorSort()
    {
        // graphql type test
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery(['sort' => 111])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('Variable "$sort" of type "Int" used in position expecting type "String".', $response->json('errors.0.message'));

        // not exist column validation test
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
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
            query Customers' . ($variableTypes ? "({$variableTypes})" : '') . ' {
                customers' . ($variableArgs ? "({$variableArgs})" : '') . ' {
                    current_page
                    from
                    has_more_pages
                    last_page
                    per_page
                    to
                    total
                    sortables
                    data {
                        id
                        created_at
                        updated_at
                    }
                }
            }
        ';

        return $query;
    }

    private function assertSuccessCustomersResponse(TestResponse $response, $total = null, $perPage = null, $page = null): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'customers' => [
                    'current_page',
                    'from',
                    'has_more_pages',
                    'last_page',
                    'per_page',
                    'to',
                    'total',
                    'sortables',
                    'data' => [
                        '*' => [
                            'id',
                            'created_at',
                            'updated_at',
                        ],
                    ],
                ],
            ],
        ]);
        $totalCount = $total ?? $this->_totalCustomersCount;
        $currentPage = $page ?? 1;
        $currentPerPage = $perPage ?? 10;
        $this->assertEquals($totalCount, $response->json('data.customers.total'));
        $this->assertEquals(Customer::$sortables, $response->json('data.customers.sortables'));
        $this->assertEquals($currentPerPage, $response->json('data.customers.per_page'));
        $this->assertEquals($currentPage, $response->json('data.customers.current_page'));
        $this->assertEquals(($currentPage - 1) * ($currentPerPage) + 1, $response->json('data.customers.from'));
        $this->assertEquals(min($totalCount, $currentPage * $currentPerPage), $response->json('data.customers.to'));
        $this->assertEquals(ceil($totalCount / $currentPerPage) > $currentPage, $response->json('data.customers.has_more_pages'));
        $this->assertEquals(ceil($totalCount / $currentPerPage), $response->json('data.customers.last_page'));
        $this->assertCount(min($totalCount - ($currentPage - 1) * $currentPerPage, $currentPerPage), $response->json('data.customers.data'));
    }
}
