<?php

namespace Tests\GraphQL\Management\Queries;

use App\Models\Company\ActivityLog;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class ActivityLogsGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private int $_totalActivityLogsCount = 30;

    private array $_structure = [
        'id',
        'admin_id',
        'created_at',
        'description',
        'admin' => [
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
                    'calculate_installment',
                    'calculator_export',
                    'installment_view_all',
                    'installment_view',
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
                    'tariff_view_all',
                    'tariff_view',
                    'tariff_edit',
                    'tariff_delete',
                    'tariff_create',
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
                ],
            ],
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();

        ActivityLog::query()->forceDelete();
        ActivityLog::factory()->count($this->_totalActivityLogsCount)->create([
            'causer_id' => $this->createAdminUser()->id,
            'causer_type' => get_class($this->createAdminUser()),
        ]);
        ActivityLog::query()->whereNull('causer_id')->forceDelete();
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

        $this->assertSuccessActivityLogsResponse($response);
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

        $this->assertSuccessActivityLogsResponse($response, page: $page);
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

        $this->assertSuccessActivityLogsResponse($response, perPage: $perPage);
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

        $this->assertSuccessActivityLogsResponse($response);
        $activityLogIds = ActivityLog::query()->orderBy('id')->limit(10)->pluck('id')->toArray();
        $this->assertEquals($activityLogIds, $response->json('data.activityLogs.data.*.id'));
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

        $this->assertSuccessActivityLogsResponse($response);
        $activityLogIds = ActivityLog::query()->orderByDesc('id')->limit(10)->pluck('id')->toArray();
        $this->assertEquals($activityLogIds, $response->json('data.activityLogs.data.*.id'));
    }

    #[Test]
    public function successSearch()
    {
        $activityLog = ActivityLog::query()->with('causer')->first();
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['search' => $activityLog->description])
            );

        $this->assertSuccessActivityLogsResponse($response, total: 1);

        $this->assertEquals($activityLog->id, $response->json('data.activityLogs.data.0.id'));
        $this->assertEquals($activityLog->causer_id, $response->json('data.activityLogs.data.0.admin_id'));
        $this->assertEquals($activityLog->created_at->timestamp, $response->json('data.activityLogs.data.0.created_at'));
        $this->assertEquals($this->logMessageBuilder($activityLog->subject_type, $activityLog->subject_id, $activityLog->event->value), $response->json('data.activityLogs.data.0.description'));
        $this->assertEquals($activityLog->causer->id, $response->json('data.activityLogs.data.0.admin.id'));
        $this->assertEquals($activityLog->causer->username, $response->json('data.activityLogs.data.0.admin.username'));
        $this->assertEquals($activityLog->causer->first_name, $response->json('data.activityLogs.data.0.admin.first_name'));
        $this->assertEquals($activityLog->causer->last_name, $response->json('data.activityLogs.data.0.admin.last_name'));
        $this->assertEquals($activityLog->causer->phone, $response->json('data.activityLogs.data.0.admin.phone'));
        $this->assertEquals($activityLog->causer->status->value, $response->json('data.activityLogs.data.0.admin.status'));
        $this->assertEquals($activityLog->causer->language, $response->json('data.activityLogs.data.0.admin.language'));
        $this->assertEquals($activityLog->causer->role_id, $response->json('data.activityLogs.data.0.admin.role_id'));
        $this->assertEquals($activityLog->causer->created_at->timestamp, $response->json('data.activityLogs.data.0.admin.created_at'));
        $this->assertEquals($activityLog->causer->updated_at?->timestamp, $response->json('data.activityLogs.data.0.admin.updated_at'));
        $this->assertEquals($activityLog->causer->deleted_at?->timestamp, $response->json('data.activityLogs.data.0.admin.deleted_at'));
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
            query ActivityLogs($sort: String, $search: String, $page: Int, $per_page: Int) {
                activityLogs(sort: $sort, search: $search, page: $page, per_page: $per_page) {
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

    private function assertSuccessActivityLogsResponse(TestResponse $response, $total = null, $perPage = null, $page = null): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'activityLogs' => [
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
        $totalCount = $total ?? $this->_totalActivityLogsCount;
        $currentPage = $page ?? 1;
        $currentPerPage = $perPage ?? 10;
        $this->assertEquals($currentPage, $response->json('data.activityLogs.current_page'));
        $this->assertEquals(($currentPage - 1) * ($currentPerPage) + 1, $response->json('data.activityLogs.from'));
        $this->assertEquals(ceil($totalCount / $currentPerPage) > $currentPage, $response->json('data.activityLogs.has_more_pages'));
        $this->assertEquals(ceil($totalCount / $currentPerPage), $response->json('data.activityLogs.last_page'));
        $this->assertEquals($currentPerPage, $response->json('data.activityLogs.per_page'));
        $this->assertEquals(min($totalCount, $currentPage * $currentPerPage), $response->json('data.activityLogs.to'));
        $this->assertEquals($totalCount, $response->json('data.activityLogs.total'));
        $this->assertCount(min($totalCount - ($currentPage - 1) * $currentPerPage, $currentPerPage), $response->json('data.activityLogs.data'));
    }

    private function convertIntoQuery(array $structure): string
    {
        foreach ($structure as $key => $value) {
            if (is_array($value)) {
                if ($key == '*') {
                    $structure[$key] = $this->convertIntoQuery($value);
                } else {
                    $structure[$key] = "{$key} {{$this->convertIntoQuery($value)}}";
                }
            }
        }

        return implode(' ', $structure);
    }

    private function logMessageBuilder($modelName, $modelId, $event): string
    {
        $modelName = trans('models.' . $modelName);

        return trans('logging.' . $event, ['entity' => $modelName, 'id' => $modelId]);
    }
}
