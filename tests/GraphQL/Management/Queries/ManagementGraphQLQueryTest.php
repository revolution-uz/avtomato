<?php

namespace Management\Queries;

use App\Models\Company\ActivityLog;
use App\Models\Company\BalanceFund;
use App\Models\Company\Branch;
use App\Models\Company\PersonalAccessToken;
use App\Models\Company\Rbac\Role;
use App\Models\Company\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class ManagementGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private int $_totalFactoryCount = 30;

    protected function setUp(): void
    {
        parent::setUp();

        User::query()->forceDelete();
        User::factory()->count($this->_totalFactoryCount)->create([
            'role_id' => Role::getAdmin()->id,
            'password' => Hash::make('password'),
        ]);
        BalanceFund::query()->forceDelete();
        BalanceFund::factory()->count($this->_totalFactoryCount)->create();
        Branch::query()->forceDelete();
        Branch::factory()->count($this->_totalFactoryCount)->create();
    }

    #[Test]
    public function activityLogs(): void
    {
        $field = 'activityLogs';

        $structure = [
            'created_at',
            'description',
            'id',
        ];

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery($field, $structure)
            );

        $this->assertResponse($response, $field, $structure, ActivityLog::count());
    }

    #[Test]
    public function adminActivityLogs(): void
    {
        $field = 'adminActivityLogs';

        $structure = [
            'created_at',
            'description',
            'id',
        ];

        $user = $this->createAdminUser();

        $response = $this
            ->actingAs($user)
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery($field, $structure, ['admin_id' => $user->id])
            );

        $this->assertResponse($response, $field, $structure, ActivityLog::count());
    }

    #[Test]
    public function adminDevices(): void
    {
        $user = $this->createAdminUser();

        PersonalAccessToken::factory()->count($this->_totalFactoryCount)->create([
            'tokenable_id' => $user->id,
            'tokenable_type' => get_class($user),
        ]);

        $field = 'adminDevices';

        $structure = [
            'created_at',
            'device_ip',
            'device_os',
            'device_type',
            'id',
            'last_used_at',
        ];

        $response = $this
            ->actingAs($user)
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery($field, $structure, ['admin_id' => $user->id]),
            );

        $this->assertResponse($response, $field, $structure);
    }

    #[Test]
    public function currentProfileDevices(): void
    {
        $user = $this->createAdminUser();

        PersonalAccessToken::factory()->count(30)->create([
            'tokenable_type' => User::class,
            'tokenable_id' => $user->id,
        ]);

        $field = 'currentProfileDevices';

        $structure = [
            'created_at',
            'device_ip',
            'device_os',
            'device_type',
            'id',
            'last_used_at',
        ];

        $response = $this
            ->actingAs($user)
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery($field, $structure),
            );

        $this->assertResponse($response, $field, $structure);
    }

    #[Test]
    public function admins(): void
    {
        $field = 'admins';

        $structure = [
            'created_at',
            'deleted_at',
            'first_name',
            'id',
            'language',
            'last_name',
            'phone',
            'status',
            'updated_at',
            'username',
        ];

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery($field, $structure),
            );

        $this->assertResponse($response, $field, $structure);
    }

    #[Test]
    public function balanceFunds(): void
    {
        $field = 'balanceFunds';

        $structure = [
            'amount',
            'card_number',
            'created_at',
            'external_transaction_id',
            'id',
        ];

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery($field, $structure),
            );

        $this->assertResponse($response, $field, $structure);
    }

    #[Test]
    public function branches(): void
    {
        $field = 'branches';

        $structure = [
            'address',
            'created_at',
            'deleted_at',
            'id',
            'inn',
            'name',
            'updated_at',
        ];

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery($field, $structure),
            );

        $this->assertResponse($response, $field, $structure);
    }

    #[Test]
    public function currentProfileActivityLogs(): void
    {
        $user = $this->createAdminUser();

        ActivityLog::factory()->count($this->_totalFactoryCount)->create([
            'causer_id' => $user->id,
        ]);

        $field = 'currentProfileActivityLogs';

        $structure = [
            'admin_id',
            'created_at',
            'description',
            'id',
        ];

        $response = $this
            ->actingAs($user)
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery($field, $structure),
            );

        $this->assertResponse($response, $field, $structure);
    }

    #[Test]
    public function roles(): void
    {
        $field = 'roles';

        $structure = [
            'created_at',
            'deleted_at',
            'id',
            'name',
            'updated_at',
        ];

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery($field, $structure),
            );

        $this->assertResponse($response, $field, $structure, $response->json("data.$field.total"));
    }

    private function buildGraphQLQuery(string $field, array $structure, array $variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $variableArgs = implode(', ', array_map(fn($variableName) => "{$variableName}: \${$variableName}", array_keys($variables)));
        $variableTypes = implode(', ', array_map(fn($variableName) => "\${$variableName}: " . $this->getGraphQLType($variables[$variableName]), array_keys($variables)));
        $dataStructure = implode(' ', $structure);
        $query['query'] = '
            query ' . ($variableTypes ? "({$variableTypes})" : '') . ' {
                ' . $field . ($variableArgs ? "({$variableArgs})" : '') . ' {
                    current_page
                    from
                    has_more_pages
                    last_page
                    per_page
                    to
                    total
                    data {' . $dataStructure . '}
                }
            }
        ';

        return $query;
    }

    private function assertResponse(TestResponse $response, string $field, array $structure, $total = null, $perPage = null, $page = null): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                $field => [
                    'current_page',
                    'from',
                    'has_more_pages',
                    'last_page',
                    'per_page',
                    'to',
                    'total',
                    'data' => [
                        '*' => $structure,
                    ],
                ],
            ],
        ]);
        $totalCount = $total ?? $this->_totalFactoryCount;
        $currentPage = $page ?? 1;
        $currentPerPage = $perPage ?? 10;
        $this->assertEquals($currentPage, $response->json("data.$field.current_page"));
        $this->assertEquals(($currentPage - 1) * ($currentPerPage) + 1, $response->json("data.$field.from"));
        $this->assertEquals(ceil($totalCount / $currentPerPage) > $currentPage, $response->json("data.$field.has_more_pages"));
        $this->assertEquals(ceil($totalCount / $currentPerPage), $response->json("data.$field.last_page"));
        $this->assertEquals($currentPerPage, $response->json("data.$field.per_page"));
        $this->assertEquals(min($totalCount, $currentPage * $currentPerPage), $response->json("data.$field.to"));
        $this->assertEquals($totalCount, $response->json("data.$field.total"));
        $this->assertCount(min($totalCount - ($currentPage - 1) * $currentPerPage, $currentPerPage), $response->json("data.$field.data"));
    }
}
