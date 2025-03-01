<?php

namespace Tests\GraphQL\Installment\Queries;

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

        $branch = Branch::query()->firstOrFail();
        Transaction::factory(10)->create([
            'transaction_type_id' => TransactionType::query()->inRandomOrder()->firstOrFail()->id,
            'branch_id' => $branch->id,
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
    public function successStatisticsWithDateRange()
    {
        $branchId = Transaction::query()->first()->branch_id;
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(from_date: $this->_carbon->format('Y-m-d'), to_date: $this->_carbon->addDay()->format('Y-m-d')),
            );
        $this->assertSuccessResponse($response);
    }

    #[Test]
    public function errorValidations()
    {
        // validation date format errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(from_date: '11.01.2024'));
        $response->assertOk();
        //        dd($response->json());
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.date_format', ['attribute' => 'from date', 'format' => 'Y-m-d']), $response->json('errors.0.extensions.validation.from_date.0'));

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(from_date: '2024-09-25', to_date: '2024-09-20'));
        $response->assertOk();
        //        dd($response->json());
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.after_or_equal', ['attribute' => 'to date', 'date' => 'from date']), $response->json('errors.0.extensions.validation.to_date.0'));

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
        $error = $response->json('errors.0.extensions.validation');
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'branch_ids.0']), $error['branch_ids.0'][0]);

        $response = $this
            ->actingAs($simpleAdmin)
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(from_date: $this->_carbon->format('Y-m-d'))
            );
        $this->assertSuccessResponse($response);
        $branchInResult = $response->json('data.StatisticsByBranches.branches');
        $this->assertCount(1, $branchInResult);
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
        $this->assertCount(2, $branchInResult);
        $this->assertEquals($branches[0]->id, $branchInResult[0]['branch']['id']);
        $this->assertEquals($branches[1]->id, $branchInResult[1]['branch']['id']);

    }

    private function buildGraphQLQuery(?array $branch_ids = null, ?string $from_date = null, ?string $to_date = null): array
    {
        $query = [];
        $query['variables'] = ['branch_ids' => $branch_ids, 'from_date' => $from_date, 'to_date' => $to_date];
        $query['query'] = '
            query StatisticsByBranches($branch_ids: [Int!], $from_date: String, $to_date: String) {
                  StatisticsByBranches(branch_ids: $branch_ids, from_date: $from_date, to_date: $to_date) {
                  full_total_amount
                  branches {
                        branch_id
                        branch {
                            id
                            name
                        }
                        total_amount_by_branch
                        types {
                        name
                        amount
                    }
                  }
                  total_amount_by_type {
                        name
                        amount
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
                            'branch' => [
                                'id',
                                'name',
                            ],
                            'total_amount_by_branch',
                            'types' => [
                                '*' => [
                                    'name',
                                    'amount',
                                ],
                            ],
                        ],
                    ],
                    'total_amount_by_type' => [
                        '*' => [
                            'name',
                            'amount',
                        ],
                    ],
                ],
            ],
        ]);
    }
}
