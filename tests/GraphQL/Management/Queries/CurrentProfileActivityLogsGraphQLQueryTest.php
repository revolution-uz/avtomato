<?php

namespace Tests\GraphQL\Management\Queries;

use App\Models\Company\ActivityLog;
use App\Models\Company\CompanyAdmin;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class CurrentProfileActivityLogsGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private int $_totalCurrentProfileActivityLogsCount = 30;

    private CompanyAdmin $_admin;

    private $_activityLogQuery;

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
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->_admin = $this->createAdminUser();

        ActivityLog::query()->forceDelete();
        ActivityLog::factory()->count($this->_totalCurrentProfileActivityLogsCount)->create([
            'causer_id' => $this->_admin->id,
            'causer_type' => get_class($this->_admin),
        ]);
        $this->_activityLogQuery = ActivityLog::query()->where('causer_id', $this->_admin->id)->where('causer_type', get_class($this->_admin));
    }

    #[Test]
    public function successWithoutArguments()
    {
        $response = $this
            ->actingAs($this->_admin)
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery()
            );

        $this->assertSuccessCurrentProfileActivityLogsResponse($response);
    }

    #[Test]
    public function successPagination()
    {
        $page = 2;

        $response = $this
            ->actingAs($this->_admin)
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['page' => $page])
            );

        $this->assertSuccessCurrentProfileActivityLogsResponse($response, page: $page);
    }

    #[Test]
    public function successPerPage()
    {
        $perPage = 25;

        $response = $this
            ->actingAs($this->_admin)
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['per_page' => $perPage])
            );

        $this->assertSuccessCurrentProfileActivityLogsResponse($response, perPage: $perPage);
    }

    #[Test]
    public function successSortAscending()
    {
        $response = $this
            ->actingAs($this->_admin)
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['sort' => 'id'])
            );

        $this->assertSuccessCurrentProfileActivityLogsResponse($response);
        $currentProfileActivityLogsIds = $this->_activityLogQuery->orderBy('id')->limit(10)->pluck('id')->toArray();
        $this->assertEquals($currentProfileActivityLogsIds, $response->json('data.currentProfileActivityLogs.data.*.id'));
    }

    #[Test]
    public function successSortDescending()
    {
        $response = $this
            ->actingAs($this->_admin)
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['sort' => '-id'])
            );

        $this->assertSuccessCurrentProfileActivityLogsResponse($response);
        $currentProfileActivityLogsIds = $this->_activityLogQuery->orderByDesc('id')->limit(10)->pluck('id')->toArray();
        $this->assertEquals($currentProfileActivityLogsIds, $response->json('data.currentProfileActivityLogs.data.*.id'));
    }

    #[Test]
    public function successSearch()
    {
        $activityLog = $this->_activityLogQuery->with('causer')->first();

        $response = $this
            ->actingAs($this->_admin)
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['search' => $activityLog->description])
            );

        $this->assertSuccessCurrentProfileActivityLogsResponse($response, total: 1);

        $this->assertEquals($activityLog->id, $response->json('data.currentProfileActivityLogs.data.0.id'));
        $this->assertEquals($activityLog->causer_id, $response->json('data.currentProfileActivityLogs.data.0.admin_id'));
        $this->assertEquals($activityLog->created_at->timestamp, $response->json('data.currentProfileActivityLogs.data.0.created_at'));
        $this->assertEquals($this->logMessageBuilder($activityLog->subject_type, $activityLog->subject_id, $activityLog->event->value), $response->json('data.currentProfileActivityLogs.data.0.description'));

        $this->assertEquals($activityLog->causer->id, $response->json('data.currentProfileActivityLogs.data.0.admin.id'));
        $this->assertEquals($activityLog->causer->username, $response->json('data.currentProfileActivityLogs.data.0.admin.username'));
        $this->assertEquals($activityLog->causer->first_name, $response->json('data.currentProfileActivityLogs.data.0.admin.first_name'));
        $this->assertEquals($activityLog->causer->last_name, $response->json('data.currentProfileActivityLogs.data.0.admin.last_name'));
        $this->assertEquals($activityLog->causer->phone, $response->json('data.currentProfileActivityLogs.data.0.admin.phone'));
        $this->assertEquals($activityLog->causer->status->value, $response->json('data.currentProfileActivityLogs.data.0.admin.status'));
        $this->assertEquals($activityLog->causer->language, $response->json('data.currentProfileActivityLogs.data.0.admin.language'));
        $this->assertEquals($activityLog->causer->role_id, $response->json('data.currentProfileActivityLogs.data.0.admin.role_id'));
        $this->assertEquals($activityLog->causer->created_at->timestamp, $response->json('data.currentProfileActivityLogs.data.0.admin.created_at'));
        $this->assertEquals($activityLog->causer->updated_at?->timestamp, $response->json('data.currentProfileActivityLogs.data.0.admin.updated_at'));
        $this->assertEquals($activityLog->causer->deleted_at?->timestamp, $response->json('data.currentProfileActivityLogs.data.0.admin.deleted_at'));
    }

    #[Test]
    public function errorPage()
    {
        $page = 'kjefnerw';
        $response = $this
            ->actingAs($this->_admin)
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['page' => $page])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals("Variable \"\$page\" got invalid value \"{$page}\"; Int cannot represent non-integer value: \"{$page}\"", $response->json('errors.0.message'));

        $page = 0;
        $response = $this
            ->actingAs($this->_admin)
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
            ->actingAs($this->_admin)
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['per_page' => $per_page])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals("Variable \"\$per_page\" got invalid value \"{$per_page}\"; Int cannot represent non-integer value: \"{$per_page}\"", $response->json('errors.0.message'));

        $per_page = 0;
        $response = $this
            ->actingAs($this->_admin)
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
            ->actingAs($this->_admin)
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
            ->actingAs($this->_admin)
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['sort' => $sort])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals("Variable \"\$sort\" got invalid value {$sort}; String cannot represent a non string value: {$sort}", $response->json('errors.0.message'));

        $sort = 'non-exist-column';
        $response = $this
            ->actingAs($this->_admin)
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
            query CurrentProfileActivityLogs($sort: String, $search: String, $page: Int, $per_page: Int) {
                currentProfileActivityLogs(sort: $sort, search: $search, page: $page, per_page: $per_page) {
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

    private function assertSuccessCurrentProfileActivityLogsResponse(TestResponse $response, $total = null, $perPage = null, $page = null): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'currentProfileActivityLogs' => [
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
        $totalCount = $total ?? $this->_totalCurrentProfileActivityLogsCount;
        $currentPage = $page ?? 1;
        $currentPerPage = $perPage ?? 10;
        $this->assertEquals($currentPage, $response->json('data.currentProfileActivityLogs.current_page'));
        $this->assertEquals(($currentPage - 1) * ($currentPerPage) + 1, $response->json('data.currentProfileActivityLogs.from'));
        $this->assertEquals(ceil($totalCount / $currentPerPage) > $currentPage, $response->json('data.currentProfileActivityLogs.has_more_pages'));
        $this->assertEquals(ceil($totalCount / $currentPerPage), $response->json('data.currentProfileActivityLogs.last_page'));
        $this->assertEquals($currentPerPage, $response->json('data.currentProfileActivityLogs.per_page'));
        $this->assertEquals(min($totalCount, $currentPage * $currentPerPage), $response->json('data.currentProfileActivityLogs.to'));
        $this->assertEquals($totalCount, $response->json('data.currentProfileActivityLogs.total'));
        $this->assertCount(min($totalCount - ($currentPage - 1) * $currentPerPage, $currentPerPage), $response->json('data.currentProfileActivityLogs.data'));
    }

    private function logMessageBuilder($modelName, $modelId, $event): string
    {
        $modelName = trans('models.' . $modelName);

        return trans('logging.' . $event, ['entity' => $modelName, 'id' => $modelId]);
    }
}
