<?php

namespace Tests\GraphQL\Management\Queries;

use App\Models\Company\Attachment;
use App\Models\Company\CompanyAdmin;
use App\Models\Company\Rbac\Role;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class AdminsGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private int $_totalAdminsCount = 30;

    private array $_structure = [
        'id',
        'username',
        'first_name',
        'last_name',
        'phone',
        'status',
        'language',
        'role_id',
        'created_at',
        'updated_at',
        'deleted_at',
        'avatar' => [
            'id',
            'type',
            'url',
            'name',
            'original_name',
            'mime',
            'created_at',
            'updated_at',
            'deleted_at',
        ],
        'template_theme' => [
            'layout_name',
        ],
        'role' => [
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
                'installment_view_document',
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
                'scoring_royxat_credit_history_view_all',
                'scoring_royxat_credit_history_view',
                'scoring_royxat_credit_history_create',
                'scoring_royxat_credit_history_delete',
                'scoring_uzum_nasiya_limit_view_all',
                'scoring_uzum_nasiya_limit_view',
                'scoring_uzum_nasiya_limit_create',
                'scoring_uzum_nasiya_limit_delete',
                'activity_log_view',
                'activity_log_view_all',
                'activity_log_view_own',
                'settings_edit',
                'ecommerce_product_category_create',
                'ecommerce_product_category_edit',
                'ecommerce_product_category_delete',
                'ecommerce_product_category_view',
                'ecommerce_product_category_view_all',
                'ecommerce_banner_create',
                'ecommerce_banner_edit',
                'ecommerce_banner_delete',
                'ecommerce_banner_view',
                'ecommerce_banner_view_all',
                'ecommerce_product_brand_create',
                'ecommerce_product_brand_edit',
                'ecommerce_product_brand_delete',
                'ecommerce_product_brand_view',
                'ecommerce_product_brand_view_all',
                'ecommerce_product_option_value_create',
                'ecommerce_product_option_value_edit',
                'ecommerce_product_option_value_delete',
                'ecommerce_product_option_value_view',
                'ecommerce_product_option_value_view_all',
                'ecommerce_product_option_group_create',
                'ecommerce_product_option_group_edit',
                'ecommerce_product_option_group_delete',
                'ecommerce_product_option_group_view',
                'ecommerce_product_option_group_view_all',
                'ecommerce_product_option_create',
                'ecommerce_product_option_edit',
                'ecommerce_product_option_delete',
                'ecommerce_product_option_view',
                'ecommerce_product_option_view_all',
                'ecommerce_product_join_option_create',
                'ecommerce_product_join_option_edit',
                'ecommerce_product_join_option_delete',
                'ecommerce_product_join_option_view',
                'ecommerce_product_join_option_view_all',
                'ecommerce_product_variation_create',
                'ecommerce_product_variation_edit',
                'ecommerce_product_variation_delete',
                'ecommerce_product_variation_view',
                'ecommerce_product_variation_view_all',
                'ecommerce_product_create',
                'ecommerce_product_edit',
                'ecommerce_product_delete',
                'ecommerce_product_view',
                'ecommerce_product_view_all',
                'ecommerce_orders_view_all',
                'ecommerce_orders_view',
                'ecommerce_order_statuses_create',
                'ecommerce_order_statuses_edit',
                'ecommerce_order_statuses_delete',
                'ecommerce_order_statuses_view',
                'ecommerce_order_statuses_view_all',
                'ecommerce_order_items_view_all',
                'ecommerce_users_view_all',
                'ecommerce_users_view',
                'ecommerce_delivery_point_create',
                'ecommerce_delivery_point_edit',
                'ecommerce_delivery_point_delete',
                'ecommerce_delivery_point_view',
                'ecommerce_delivery_point_view_all',
                'ecommerce_images_create',
                'loyalty_program_users_view_all',
                'loyalty_program_users_view',
                'loyalty_program_tariff_view_all',
                'loyalty_program_tariff_view',
                'loyalty_program_tariff_edit',
                'loyalty_program_tariff_delete',
                'loyalty_program_tariff_create',
            ],
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();

        CompanyAdmin::query()->forceDelete();
        CompanyAdmin::factory()->create([
            'role_id' => Role::getAdmin()->id,
        ]);
        CompanyAdmin::factory()->count($this->_totalAdminsCount - 1)->create();
        foreach (CompanyAdmin::all() as $admin) {
            Attachment::factory()->create([
                'attachmentable_id' => $admin->id,
                'attachmentable_type' => CompanyAdmin::class,
            ]);
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

        $this->assertSuccessAdminsResponse($response);
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

        $this->assertSuccessAdminsResponse($response, page: $page);
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

        $this->assertSuccessAdminsResponse($response, perPage: $perPage);
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

        $this->assertSuccessAdminsResponse($response);
        $adminIds = CompanyAdmin::query()->orderBy('id')->limit(10)->pluck('id')->toArray();
        $this->assertEquals($adminIds, $response->json('data.admins.data.*.id'));
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

        $this->assertSuccessAdminsResponse($response);
        $adminIds = CompanyAdmin::query()->orderByDesc('id')->limit(10)->pluck('id')->toArray();
        $this->assertEquals($adminIds, $response->json('data.admins.data.*.id'));
    }

    #[Test]
    public function successSearch()
    {
        $admin = CompanyAdmin::query()->with(['avatar', 'role'])->first();
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['search' => $admin->username])
            );

        $this->assertSuccessAdminsResponse($response, total: 1);

        $this->assertEquals($admin->id, $response->json('data.admins.data.0.id'));
        $this->assertEquals($admin->username, $response->json('data.admins.data.0.username'));
        $this->assertEquals($admin->first_name, $response->json('data.admins.data.0.first_name'));
        $this->assertEquals($admin->last_name, $response->json('data.admins.data.0.last_name'));
        $this->assertEquals($admin->phone, $response->json('data.admins.data.0.phone'));
        $this->assertEquals($admin->status->value, $response->json('data.admins.data.0.status'));
        $this->assertEquals($admin->language, $response->json('data.admins.data.0.language'));
        $this->assertEquals($admin->role_id, $response->json('data.admins.data.0.role_id'));
        $this->assertEquals($admin->created_at->timestamp, $response->json('data.admins.data.0.created_at'));
        $this->assertEquals($admin->updated_at?->timestamp, $response->json('data.admins.data.0.updated_at'));
        $this->assertEquals($admin->deleted_at?->timestamp, $response->json('data.admins.data.0.deleted_at'));

        $this->assertEquals($admin->avatar->id, $response->json('data.admins.data.0.avatar.id'));
        $this->assertEquals($admin->avatar->type->value, $response->json('data.admins.data.0.avatar.type'));
        $this->assertEquals($admin->avatar->url, $response->json('data.admins.data.0.avatar.url'));
        $this->assertEquals($admin->avatar->name, $response->json('data.admins.data.0.avatar.name'));
        $this->assertEquals($admin->avatar->original_name, $response->json('data.admins.data.0.avatar.original_name'));
        $this->assertEquals($admin->avatar->mime, $response->json('data.admins.data.0.avatar.mime'));
        $this->assertEquals($admin->avatar->created_at->timestamp, $response->json('data.admins.data.0.avatar.created_at'));
        $this->assertEquals($admin->avatar->updated_at?->timestamp, $response->json('data.admins.data.0.avatar.updated_at'));
        $this->assertEquals($admin->avatar->deleted_at?->timestamp, $response->json('data.admins.data.0.avatar.deleted_at'));

        $this->assertEquals($admin->template_theme->layout_name, $response->json('data.admins.data.0.template_theme.layout_name'));

        $this->assertEquals($admin->role->id, $response->json('data.admins.data.0.role.id'));
        $this->assertEquals($admin->role->name, $response->json('data.admins.data.0.role.name'));
        $this->assertEquals($admin->role->created_at->timestamp, $response->json('data.admins.data.0.role.created_at'));
        $this->assertEquals($admin->role->updated_at?->timestamp, $response->json('data.admins.data.0.role.updated_at'));
        $this->assertEquals($admin->role->deleted_at?->timestamp, $response->json('data.admins.data.0.role.deleted_at'));

        $permissionGroups = $admin->role->mapAllPermissionByBranch();
        $this->assertEquals($permissionGroups[0]['id'], $response->json('data.admins.data.0.role.permission_groups.0.id'));
        $this->assertEquals($permissionGroups[0]['slug']->value, $response->json('data.admins.data.0.role.permission_groups.0.slug'));
        $this->assertEquals($permissionGroups[0]['name'], $response->json('data.admins.data.0.role.permission_groups.0.name'));

        foreach ($admin->role->mapRolePermissionByBranch() as $key => $value) {
            $this->assertEquals(json_encode($value), $response->json("data.admins.data.0.role.permissions.{$key}"));
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
            query Admins($sort: String, $search: String, $page: Int, $per_page: Int) {
                admins(sort: $sort, search: $search, page: $page, per_page: $per_page) {
                    total
                    per_page
                    current_page
                    from
                    to
                    last_page
                    has_more_pages
                    data {' . $this->convertIntoQuery($this->_structure) . '}
                }
            }
        ';

        return $query;
    }

    private function assertSuccessAdminsResponse(TestResponse $response, $total = null, $perPage = null, $page = null): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'admins' => [
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
        $totalCount = $total ?? $this->_totalAdminsCount;
        $currentPage = $page ?? 1;
        $currentPerPage = $perPage ?? 10;
        $this->assertEquals($currentPage, $response->json('data.admins.current_page'));
        $this->assertEquals(($currentPage - 1) * ($currentPerPage) + 1, $response->json('data.admins.from'));
        $this->assertEquals(ceil($totalCount / $currentPerPage) > $currentPage, $response->json('data.admins.has_more_pages'));
        $this->assertEquals(ceil($totalCount / $currentPerPage), $response->json('data.admins.last_page'));
        $this->assertEquals($currentPerPage, $response->json('data.admins.per_page'));
        $this->assertEquals(min($totalCount, $currentPage * $currentPerPage), $response->json('data.admins.to'));
        $this->assertEquals($totalCount, $response->json('data.admins.total'));
        $this->assertCount(min($totalCount - ($currentPage - 1) * $currentPerPage, $currentPerPage), $response->json('data.admins.data'));
    }
}
