<?php

namespace Tests\GraphQL\Management\Queries;

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
        'permissions' => [
            'profile_edit',
            'profile_change_password',
            'profile_change_language',
            'profile_change_avatar',
            'profile_devices',
            'profile_revoke_device',
            'balance_fund_view_all',
            'balance_fund_view',
            'balance_fund_delete',
            'balance_fund_create',
            'balance_fund_confirm',
            'balance_charge_view_all',
            'balance_charge_view',
            'balance_charge_delete',
            'balance_charge_create',
            'balance_charge_confirm',
            'admin_view_all',
            'admin_view',
            'admin_create',
            'admin_edit',
            'admin_delete',
            'admin_change_password',
            'admin_change_avatar',
            'admin_change_role',
            'admin_devices',
            'admin_revoke_device',
            'profile_change_theme',
            'admin_change_theme',
            'role_view_all',
            'role_view',
            'role_create',
            'role_edit',
            'role_delete',
            'role_permissions',
            'customer_info_by_passport',
            'customer_create',
            'customer_edit',
            'customer_view_all',
            'customer_view',
            'customer_view_installments',
            'customer_card_view',
            'customer_card_edit',
            'customer_card_remove',
            'customer_tag_attach',
            'customer_tag_detach',
            'customer_tag_view',
            'tag_create',
            'tag_edit',
            'tag_delete',
            'tag_view',
            'tag_view_all',
            'calculate_installment',
            'calculator_export',
            'installment_view_all',
            'installment_view',
            'installment_view_statistics',
            'installment_start',
            'installment_cancel',
            'installment_freeze',
            'installment_finish',
            'installment_customer_list',
            'installment_create',
            'installment_edit',
            'installment_delete',
            'installment_attach_card',
            'installment_detach_card',
            'installment_attach_guarantor',
            'installment_update_guarantor',
            'installment_detach_guarantor',
            'installment_create_comment',
            'installment_create_transaction',
            'installment_update_comment',
            'installment_delete_comment',
            'installment_upload_document',
            'installment_delete_document',
            'dashboard_info_view',
            'tariff_view_all',
            'tariff_view',
            'tariff_edit',
            'tariff_delete',
            'tariff_create',
            'transaction_attempt_view_all',
            'transaction_view_all',
            'transaction_view',
            'transaction_create',
            'transaction_edit',
            'transaction_delete',
            'transaction_cancel',
            'transaction_type_view_all',
            'transaction_type_view',
            'transaction_type_create',
            'transaction_type_edit',
            'transaction_type_delete',
            'branch_view_all',
            'branch_view',
            'branch_edit',
            'branch_delete',
            'branch_create',
            'statistics_by_branches',
            'scoring_credit_history_view_all',
            'scoring_credit_history_view',
            'scoring_credit_history_create',
            'scoring_credit_history_delete',
            'scoring_inps_history_view_all',
            'scoring_inps_history_view',
            'scoring_inps_history_create',
            'scoring_inps_history_delete',
            'scoring_pension_info_view_all',
            'scoring_pension_info_view',
            'scoring_pension_info_create',
            'scoring_pension_info_delete',
            'scoring_mib_info_view_all',
            'scoring_mib_info_view',
            'scoring_mib_info_create',
            'scoring_mib_info_delete',
            'scoring_tax_info_view_all',
            'scoring_tax_info_view',
            'scoring_tax_info_create',
            'scoring_tax_info_delete',
            'scoring_humo_view_all',
            'scoring_humo_view',
            'scoring_humo_create',
            'scoring_humo_delete',
            'scoring_uzcard_view_all',
            'scoring_uzcard_view',
            'scoring_uzcard_create',
            'scoring_uzcard_delete',
            'activity_log_view',
            'activity_log_view_all',
            'activity_log_view_own',
            'settings_edit',
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();

        Role::factory()->count(30)->create();
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
}
