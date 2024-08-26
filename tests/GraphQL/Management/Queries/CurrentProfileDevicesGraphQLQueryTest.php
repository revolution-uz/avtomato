<?php

namespace Tests\GraphQL\Management\Queries;

use App\Models\Company\CompanyAdmin;
use App\Models\Company\PersonalAccessToken;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class CurrentProfileDevicesGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private int $_totalCurrentProfileDevicesCount = 30;

    private CompanyAdmin $_admin;

    private array $_structure = [
        'id',
        'name',
        'device_ip',
        'device_type',
        'device_os',
        'created_at',
        'last_used_at',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->_admin = $this->createAdminUser();

        PersonalAccessToken::factory()->count($this->_totalCurrentProfileDevicesCount)->create([
            'tokenable_id' => $this->_admin->id,
            'tokenable_type' => get_class($this->_admin),
        ]);
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

        $this->assertSuccessCurrentProfileDevicesResponse($response);
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

        $this->assertSuccessCurrentProfileDevicesResponse($response, page: $page);
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

        $this->assertSuccessCurrentProfileDevicesResponse($response, perPage: $perPage);
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

        $this->assertSuccessCurrentProfileDevicesResponse($response);
        $currentProfileDeviceIds = PersonalAccessToken::query()->orderBy('id')->limit(10)->pluck('id')->toArray();
        $this->assertEquals($currentProfileDeviceIds, $response->json('data.currentProfileDevices.data.*.id'));
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

        $this->assertSuccessCurrentProfileDevicesResponse($response);
        $currentProfileDeviceIds = PersonalAccessToken::query()->orderByDesc('id')->limit(10)->pluck('id')->toArray();
        $this->assertEquals($currentProfileDeviceIds, $response->json('data.currentProfileDevices.data.*.id'));
    }

    #[Test]
    public function successSearch()
    {
        $currentProfileDevice = PersonalAccessToken::factory()->create([
            'tokenable_id' => $this->_admin->id,
            'tokenable_type' => get_class($this->_admin),
            'name' => 'my-name',
            'device_ip' => 'my-device-ip',
            'device_type' => 'my-device-type',
            'device_os' => 'my-device-os',
        ]);

        $response = $this
            ->actingAs($this->_admin)
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['search' => $currentProfileDevice->name])
            );
        $this->assertSuccessCurrentProfileDevicesResponse($response, total: 1);
        $this->assertEquals($currentProfileDevice->id, $response->json('data.currentProfileDevices.data.0.id'));

        $response = $this
            ->actingAs($this->_admin)
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['search' => $currentProfileDevice->name])
            );
        $this->assertSuccessCurrentProfileDevicesResponse($response, total: 1);
        $this->assertEquals($currentProfileDevice->id, $response->json('data.currentProfileDevices.data.0.id'));

        $response = $this
            ->actingAs($this->_admin)
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['search' => $currentProfileDevice->device_ip])
            );
        $this->assertSuccessCurrentProfileDevicesResponse($response, total: 1);
        $this->assertEquals($currentProfileDevice->id, $response->json('data.currentProfileDevices.data.0.id'));

        $response = $this
            ->actingAs($this->_admin)
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['search' => $currentProfileDevice->device_type])
            );
        $this->assertSuccessCurrentProfileDevicesResponse($response, total: 1);
        $this->assertEquals($currentProfileDevice->id, $response->json('data.currentProfileDevices.data.0.id'));

        $response = $this
            ->actingAs($this->_admin)
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['search' => $currentProfileDevice->device_os])
            );
        $this->assertSuccessCurrentProfileDevicesResponse($response, total: 1);
        $this->assertEquals($currentProfileDevice->id, $response->json('data.currentProfileDevices.data.0.id'));

        $this->assertEquals($currentProfileDevice->name, $response->json('data.currentProfileDevices.data.0.name'));
        $this->assertEquals($currentProfileDevice->device_ip, $response->json('data.currentProfileDevices.data.0.device_ip'));
        $this->assertEquals($currentProfileDevice->device_type, $response->json('data.currentProfileDevices.data.0.device_type'));
        $this->assertEquals($currentProfileDevice->device_os, $response->json('data.currentProfileDevices.data.0.device_os'));
        $this->assertEquals($currentProfileDevice->created_at->timestamp, $response->json('data.currentProfileDevices.data.0.created_at'));
        $this->assertEquals($currentProfileDevice->last_used_at?->timestamp, $response->json('data.currentProfileDevices.data.0.last_used_at'));
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
            query CurrentProfileDevices($sort: String, $search: String, $page: Int, $per_page: Int) {
                currentProfileDevices(sort: $sort, search: $search, page: $page, per_page: $per_page) {
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

    private function assertSuccessCurrentProfileDevicesResponse(TestResponse $response, $total = null, $perPage = null, $page = null): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'currentProfileDevices' => [
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
        $totalCount = $total ?? $this->_totalCurrentProfileDevicesCount;
        $currentPage = $page ?? 1;
        $currentPerPage = $perPage ?? 10;
        $this->assertEquals($currentPage, $response->json('data.currentProfileDevices.current_page'));
        $this->assertEquals(($currentPage - 1) * ($currentPerPage) + 1, $response->json('data.currentProfileDevices.from'));
        $this->assertEquals(ceil($totalCount / $currentPerPage) > $currentPage, $response->json('data.currentProfileDevices.has_more_pages'));
        $this->assertEquals(ceil($totalCount / $currentPerPage), $response->json('data.currentProfileDevices.last_page'));
        $this->assertEquals($currentPerPage, $response->json('data.currentProfileDevices.per_page'));
        $this->assertEquals(min($totalCount, $currentPage * $currentPerPage), $response->json('data.currentProfileDevices.to'));
        $this->assertEquals($totalCount, $response->json('data.currentProfileDevices.total'));
        $this->assertCount(min($totalCount - ($currentPage - 1) * $currentPerPage, $currentPerPage), $response->json('data.currentProfileDevices.data'));
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
}
