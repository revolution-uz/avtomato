<?php

namespace Tests\GraphQL\Management\Queries;

use App\Enums\Company\Rbac\PermissionEnum;
use App\Models\Company\Rbac\Role;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class RolesGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private array $_structure = [
        'id',
        'name',
        'created_at',
        'updated_at',
        'deleted_at',
        'permission_groups' => [
            '*' => [
                'id',
                'slug',
                'name',
                'permissions' => [
                    '*' => [
                        'id',
                        'slug',
                        'name',
                        'branches' => [
                            '*' => [
                                'id',
                                'name',
                                'editable',
                                'enabled',
                            ],
                        ],
                    ],
                ],
            ],
        ],
        'permissions' => null,
    ];

    protected function setUp(): void
    {
        parent::setUp();

        Role::factory()->count(30)->create();

        if (empty($this->_structure['permissions'])) {
            $this->_structure['permissions'] = array_map(fn ($case) => $case->value, PermissionEnum::cases());
        }
    }

    #[Test]
    public function successWithoutArguments()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery()
            );

        $this->assertSuccessRolesResponse($response);
        foreach ($response->json('data') as $roles) {
            foreach ($roles['data'] as $role) {
                $this->checkIsNullPermission($role['permissions']);
            }
        }
    }

    #[Test]
    public function successPagination()
    {
        $page = 2;

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['page' => $page])
            );

        $this->assertSuccessRolesResponse($response, page: $page);
    }

    #[Test]
    public function successPerPage()
    {
        $perPage = 25;

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['per_page' => $perPage])
            );

        $this->assertSuccessRolesResponse($response, perPage: $perPage);
    }

    #[Test]
    public function successSortAscending()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['sort' => 'id'])
            );

        $this->assertSuccessRolesResponse($response);
        $roleIds = Role::query()->orderBy('id')->limit(10)->pluck('id')->toArray();
        $this->assertEquals($roleIds, $response->json('data.roles.data.*.id'));
    }

    #[Test]
    public function successSortDescending()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['sort' => '-id'])
            );

        $this->assertSuccessRolesResponse($response);
        $roleIds = Role::query()->orderByDesc('id')->limit(10)->pluck('id')->toArray();
        $this->assertEquals($roleIds, $response->json('data.roles.data.*.id'));
    }

    #[Test]
    public function successSearch()
    {
        $role = Role::query()->orderBy('id')->first();

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['search' => $role->name])
            );

        $this->assertSuccessRolesResponse($response, total: 1);

        $this->assertEquals($role->id, $response->json('data.roles.data.0.id'));
        $this->assertEquals($role->name, $response->json('data.roles.data.0.name'));
        $this->assertEquals($role->created_at->timestamp, $response->json('data.roles.data.0.created_at'));
        $this->assertEquals($role->updated_at?->timestamp, $response->json('data.roles.data.0.updated_at'));
        $this->assertEquals($role->deleted_at?->timestamp, $response->json('data.roles.data.0.deleted_at'));

        $permissionGroups = $role->mapAllPermissionByBranch();
        $this->assertEquals($permissionGroups[0]['id'], $response->json('data.roles.data.0.permission_groups.0.id'));
        $this->assertEquals($permissionGroups[0]['slug']->value, $response->json('data.roles.data.0.permission_groups.0.slug'));
        $this->assertEquals($permissionGroups[0]['name'], $response->json('data.roles.data.0.permission_groups.0.name'));

        $permissions = $permissionGroups[0]['permissions'];
        $this->assertEquals($permissions[0]['id'], $response->json('data.roles.data.0.permission_groups.0.permissions.0.id'));
        $this->assertEquals($permissions[0]['slug']->value, $response->json('data.roles.data.0.permission_groups.0.permissions.0.slug'));
        $this->assertEquals($permissions[0]['name'], $response->json('data.roles.data.0.permission_groups.0.permissions.0.name'));

        $branches = $permissions[0]['branches'];
        $this->assertEquals($branches[0]['id'], $response->json('data.roles.data.0.permission_groups.0.permissions.0.branches.0.id'));
        $this->assertEquals($branches[0]['name'], $response->json('data.roles.data.0.permission_groups.0.permissions.0.branches.0.name'));
        $this->assertEquals($branches[0]['editable'], $response->json('data.roles.data.0.permission_groups.0.permissions.0.branches.0.editable'));
        $this->assertEquals($branches[0]['enabled'], $response->json('data.roles.data.0.permission_groups.0.permissions.0.branches.0.enabled'));

        foreach ($role->mapRolePermissionByBranch() as $key => $value) {
            $this->assertEquals(json_encode($value), $response->json("data.roles.data.0.permissions.{$key}"));
        }
    }

    #[Test]
    public function errorPage()
    {
        $page = 'kjefnerw';
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['page' => $page])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals("Variable \"\$page\" got invalid value \"{$page}\"; Int cannot represent non-integer value: \"{$page}\"", $response->json('errors.0.message'));

        $page = 0;
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['page' => $page])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.min.numeric', ['attribute' => 'page', 'min' => 1]), $response->json('errors.0.extensions.validation.page.0'));
    }

    #[Test]
    public function errorPerPage()
    {
        $per_page = 'kjefnerw';
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['per_page' => $per_page])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals("Variable \"\$per_page\" got invalid value \"{$per_page}\"; Int cannot represent non-integer value: \"{$per_page}\"", $response->json('errors.0.message'));

        $per_page = 0;
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['per_page' => $per_page])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.min.numeric', ['attribute' => 'per page', 'min' => 5]), $response->json('errors.0.extensions.validation.per_page.0'));

        $per_page = 101;
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['per_page' => $per_page])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.max.numeric', ['attribute' => 'per page', 'max' => 100]), $response->json('errors.0.extensions.validation.per_page.0'));
    }

    #[Test]
    public function errorSort()
    {
        $sort = 111;
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['sort' => $sort])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals("Variable \"\$sort\" got invalid value {$sort}; String cannot represent a non string value: {$sort}", $response->json('errors.0.message'));

        $sort = 'non-exist-column';
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['sort' => $sort])
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
        $query['query'] = '
            query Roles($sort: String, $search: String, $page: Int, $per_page: Int) {
                roles(sort: $sort, search: $search, page: $page, per_page: $per_page) {
                    total
                    per_page
                    current_page
                    from
                    to
                    last_page
                    has_more_pages
                    sortables
                    data {' . $this->convertIntoQuery($this->_structure) . '}
                }
            }
        ';

        return $query;
    }

    private function assertSuccessRolesResponse(TestResponse $response, $total = null, $perPage = null, $page = null): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'roles' => [
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
        $totalCount = $total ?? Role::query()->count();
        $currentPage = $page ?? 1;
        $currentPerPage = $perPage ?? 10;
        $this->assertEquals($currentPage, $response->json('data.roles.current_page'));
        $this->assertEquals(($currentPage - 1) * ($currentPerPage) + 1, $response->json('data.roles.from'));
        $this->assertEquals(ceil($totalCount / $currentPerPage) > $currentPage, $response->json('data.roles.has_more_pages'));
        $this->assertEquals(ceil($totalCount / $currentPerPage), $response->json('data.roles.last_page'));
        $this->assertEquals($currentPerPage, $response->json('data.roles.per_page'));
        $this->assertEquals(min($totalCount, $currentPage * $currentPerPage), $response->json('data.roles.to'));
        $this->assertEquals($totalCount, $response->json('data.roles.total'));
        $this->assertCount(min($totalCount - ($currentPage - 1) * $currentPerPage, $currentPerPage), $response->json('data.roles.data'));
    }

    private function checkIsNullPermission(array $permissions): void
    {
        foreach ($permissions as $permission) {
            $this->assertNull($permission);
        }
    }
}
