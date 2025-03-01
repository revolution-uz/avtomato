<?php

namespace Tests\GraphQL\Installment\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Models\Company\Branch;
use App\Models\Company\Customer;
use App\Models\Company\Installment;
use App\Models\Company\Rbac\Permission;
use App\Models\Company\Rbac\Role;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class InstallmentsGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private int $_totalInstallmentsCount = 31;

    protected function setUp(): void
    {
        parent::setUp();

        Installment::query()->forceDelete();
        $branch = Branch::query()->firstOrFail();
        Installment::factory()->count($this->_totalInstallmentsCount)->create(['branch_id' => $branch->id]);
    }

    #[Test]
    public function successWithoutArguments()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery()
            );

        $this->assertSuccessInstallmentsResponse($response);
    }

    #[Test]
    public function successPagination()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(['page' => 4])
            );

        $this->assertSuccessInstallmentsResponse($response, page: 4);
    }

    #[Test]
    public function successPerPage()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(['per_page' => 25])
            );

        $this->assertSuccessInstallmentsResponse($response, perPage: 25);
    }

    #[Test]
    public function successSortAscending()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(['sort' => 'id'])
            );

        $this->assertSuccessInstallmentsResponse($response);
        $installmentIds = Installment::orderBy('id')->limit(10)->pluck('id')->toArray();
        $this->assertEquals($installmentIds, $response->json('data.installments.data.*.id'));
    }

    #[Test]
    public function successSortDescending()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(['sort' => '-id'])
            );

        $this->assertSuccessInstallmentsResponse($response);
        $installmentIds = Installment::orderByDesc('id')->limit(10)->pluck('id')->toArray();
        $this->assertEquals($installmentIds, $response->json('data.installments.data.*.id'));
    }

    #[Test]
    public function successSearch()
    {
        $input = [
            'first_name' => 'YO‘LDOSHBOY',
            'last_name' => 'SULTONOV',
            'middle_name' => 'O‘RAZMETBOYEVICH',
        ];

        /** @var Customer $customer */
        $customer = Customer::factory()->create($input);
        /** @var Installment $installment */
        $installment = Installment::factory()->create(['customer_id' => $customer->id]);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(['search' => $customer->first_name])
            );
        $this->assertSuccessInstallmentsResponse($response, total: 1);
        $this->assertEquals($installment->id, $response->json('data.installments.data.0.id'));

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(['search' => $customer->last_name])
            );
        $this->assertSuccessInstallmentsResponse($response, total: 1);
        $this->assertEquals($installment->id, $response->json('data.installments.data.0.id'));

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(['search' => $customer->middle_name])
            );
        $this->assertSuccessInstallmentsResponse($response, total: 1);
        $this->assertEquals($installment->id, $response->json('data.installments.data.0.id'));
    }

    #[Test]
    public function errorPage()
    {
        // graphql type test
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(['page' => 'asnjfasnjfasn'])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('Variable "$page" of type "String!" used in position expecting type "Int".', $response->json('errors.0.message'));

        // minimum validation test
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
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
                route('graphql.installment'),
                $this->buildGraphQLQuery(['per_page' => 'asnjfasnjfasn'])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('Variable "$per_page" of type "String!" used in position expecting type "Int".', $response->json('errors.0.message'));

        // minimum validation test
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
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
                route('graphql.installment'),
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
                route('graphql.installment'),
                $this->buildGraphQLQuery(['sort' => 111])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('Variable "$sort" of type "Int!" used in position expecting type "String".', $response->json('errors.0.message'));

        // not exist column validation test
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(['sort' => 'non-exist-column'])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.regex', ['attribute' => 'sort']), $response->json('errors.0.extensions.validation.sort.0'));
    }

    #[Test]
    public function successCheckForPermission()
    {
        $role = Role::factory()->create();
        $simpleAdmin = $this->createSimpleAdminUser($role->id);
        $permission = Permission::query()->where('slug', PermissionEnum::INSTALLMENT_VIEW_ALL)->firstOrFail();

        $branches = Branch::factory()->count(2)->create();
        Installment::factory()->count(5)->create(['branch_id' => $branches[1]->id]);

        $role->permissions()->attach($permission, ['branch_id' => $branches[0]->id]);
        $response = $this
            ->actingAs($simpleAdmin)
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery());
        $response->assertOk();
        $response->assertJsonIsObject();
        $this->assertArrayHasKey('total', $response->json('data.installments'));
        $this->assertEquals(0, $response->json('data.installments.total'));

        $role->permissions()->attach($permission, ['branch_id' => $branches[1]->id]);
        $response = $this
            ->actingAs($simpleAdmin)
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery());
        $this->assertSuccessInstallmentsResponse($response, total: 5);
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $variableArgs = implode(', ', array_map(fn ($variableName) => "{$variableName}: \${$variableName}", array_keys($variables)));
        $variableTypes = implode(', ', array_map(fn ($variableName) => "\${$variableName}: " . $this->getGraphQLType($variables[$variableName]), array_keys($variables)));
        $query['query'] = '
            query Installments' . ($variableTypes ? "({$variableTypes})" : '') . ' {
                installments' . ($variableArgs ? "({$variableArgs})" : '') . ' {
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

    private function assertSuccessInstallmentsResponse(TestResponse $response, $total = null, $perPage = null, $page = null): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'installments' => [
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
        $totalCount = $total ?? $this->_totalInstallmentsCount;
        $currentPage = $page ?? 1;
        $currentPerPage = $perPage ?? 10;
        $this->assertEquals($totalCount, $response->json('data.installments.total'));
        $this->assertEquals(Installment::$sortables, $response->json('data.installments.sortables'));
        $this->assertEquals($currentPerPage, $response->json('data.installments.per_page'));
        $this->assertEquals($currentPage, $response->json('data.installments.current_page'));
        $this->assertEquals(($currentPage - 1) * ($currentPerPage) + 1, $response->json('data.installments.from'));
        $this->assertEquals(min($totalCount, $currentPage * $currentPerPage), $response->json('data.installments.to'));
        $this->assertEquals(ceil($totalCount / $currentPerPage) > $currentPage, $response->json('data.installments.has_more_pages'));
        $this->assertEquals(ceil($totalCount / $currentPerPage), $response->json('data.installments.last_page'));
        $this->assertCount(min($totalCount - ($currentPage - 1) * $currentPerPage, $currentPerPage), $response->json('data.installments.data'));
    }
}
