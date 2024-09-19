<?php

namespace GraphQL\Installment\Queries;

use App\Enums\Company\InstallmentStatusEnum;
use App\Enums\Company\Rbac\PermissionEnum;
use App\Models\Company\Branch;
use App\Models\Company\Installment;
use App\Models\Company\Rbac\Permission;
use App\Models\Company\Rbac\Role;
use App\Models\Company\Transaction;
use App\Models\Company\TransactionType;
use Database\Seeders\Company\TransactionTypeSeeder;
use Illuminate\Support\Carbon;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class StatisticsByBranchesGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private Carbon $_carbon;

    protected function setUp(): void
    {
        parent::setUp();

        Installment::query()->forceDelete();
        $this->seed(TransactionTypeSeeder::class);
        $this->_carbon = Carbon::now();

        $this->_input = [
            'amount' => 1000000,
            'percent' => 15,
            'initial_fee' => 100000,
            'period' => 3,
            'starts_in' => Carbon::now(),
            'status' => InstallmentStatusEnum::ACTIVE,
        ];

        Transaction::factory(10)->create([
            'transaction_type_id' => TransactionType::query()->inRandomOrder()->firstOrFail()->id,
        ]);
    }

    #[Test]
    public function successStatistics()
    {

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery()
            );
        $this->assertSuccessResponse($response);
    }

    #[Test]
    public function successStatisticsWithBranchId()
    {
        $branchId = Transaction::query()->first()->branch_id;
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(branch_ids: [$branchId]),
            );
        $this->assertSuccessResponse($response);
    }

    #[Test]
    public function errorValidations()
    {
        // validation date format errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(date: '11.01.2024'));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.date_format', ['attribute' => 'date', 'format' => 'Y-m-d']), $response->json('errors.0.extensions.validation.date.0'));

        // branch id not related to company
        $branch = Branch::factory()->create();
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(branch_ids: [$branch->id])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'branch ids']), $response->json('errors.0.extensions.validation.branch_ids.0'));

    }

    #[Test]
    public function successCheckForPermission()
    {
        /** @var Role $role */
        $role = Role::factory()->create();
        $simpleAdmin = $this->createSimpleAdminUser($role->id);
        $permission = Permission::query()->where('slug', PermissionEnum::STATISTICS_BY_BRANCHES)->firstOrFail();
        $branches = Branch::factory()->count(2)->create();
        Transaction::query()->first()->update(['branch_id' => $branches[1]->id]);
        $role->permissions()->attach($permission, ['branch_id' => $branches[0]->id]);

        //no permission branch id
        $response = $this
            ->actingAs($simpleAdmin)
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(branch_ids: [$branches[1]->id])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'branch ids']), $response->json('errors.0.extensions.validation.branch_ids.0'));

        $response = $this
            ->actingAs($simpleAdmin)
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(date: $this->_carbon->format('Y-m-d'))
            );
        $this->assertSuccessResponse($response);
        $branchInResult = $response->json('data.StatisticsByBranches.branches');
        $this->assertCount(0, $branchInResult);
        $this->assertEquals('0.00', $response->json('data.StatisticsByBranches.full_total_amount'));

        $role->permissions()->attach($permission, ['branch_id' => $branches[1]->id]);
        $response = $this
            ->actingAs($simpleAdmin)
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery()
            );
        $this->assertSuccessResponse($response);
        $branchInResult = $response->json('data.StatisticsByBranches.branches');
        $this->assertCount(1, $branchInResult);
        $this->assertEquals($branches[1]->id, $branchInResult[0]['branch_id']);

    }

    private function buildGraphQLQuery(?array $branch_ids = null, ?string $date = null): array
    {
        $query = [];
        $query['variables'] = ['branch_ids' => $branch_ids, 'date' => $date];
        $query['query'] = '
            query StatisticsByBranches($branch_ids: [Int], $date: String) {
                  StatisticsByBranches(branch_ids: $branch_ids, date: $date) {
                  full_total_amount
                  branches {
                        branch_id
                        branch_name
                        uzcard_total_amount
                        humo_total_amount
                        total_amount_by_branch
                        types {
                        type
                        total_amount
                    }
                  }
                  total_amount_by_type {
                        type
                        total_amount
                  }
                }
            }
        ';

        return $query;
    }

    private function assertSuccessResponse(TestResponse $response): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'StatisticsByBranches' => [
                    'full_total_amount',
                    'branches' => [
                        '*' => [
                            'branch_id',
                            'branch_name',
                            'uzcard_total_amount',
                            'humo_total_amount',
                            'total_amount_by_branch',
                            'types' => [
                                '*' => [
                                    'type',
                                    'total_amount',
                                ],
                            ],
                        ],
                    ],
                    'total_amount_by_type' => [
                        '*' => [
                            'type',
                            'total_amount',
                        ],
                    ],
                ],
            ],
        ]);
    }
}
