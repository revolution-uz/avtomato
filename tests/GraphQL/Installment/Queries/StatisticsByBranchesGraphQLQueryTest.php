<?php

namespace GraphQL\Installment\Queries;

use App\Enums\Company\InstallmentStatusEnum;
use App\Enums\Company\Rbac\PermissionEnum;
use App\Models\Company\Branch;
use App\Models\Company\Installment;
use App\Models\Company\Rbac\Permission;
use App\Models\Company\Rbac\Role;
use App\Models\Company\Transaction;
use Illuminate\Support\Carbon;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class StatisticsByBranchesGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private array $_input;
    private Carbon $_carbon;

    protected function setUp(): void
    {
        parent::setUp();

        Installment::query()->forceDelete();

        $this->_carbon = Carbon::now();

        $this->_input = [
            'amount' => 1000000,
            'percent' => 15,
            'initial_fee' => 100000,
            'period' => 3,
            'starts_in' => Carbon::now(),
            'status' => InstallmentStatusEnum::ACTIVE,
        ];

        /** @var Installment[] $installments */
        $installments = [];

        $installments[] = Installment::factory()->create(array_merge($this->_input, ['starts_in' => Carbon::now()->subMonth()]));
        $installments[] = Installment::factory()->create($this->_input);
        $installments[] = Installment::factory()->create($this->_input);
        $installments[] = Installment::factory()->create(array_merge($this->_input, ['starts_in' => Carbon::now()->subDays(1)]));
        $installments[] = Installment::factory()->create(array_merge($this->_input, ['starts_in' => Carbon::now()->subDays(2)]));
        $installments[] = Installment::factory()->create(array_merge($this->_input, ['starts_in' => Carbon::now()->subDays(3)]));
        Transaction::factory()->create(['installment_id' => $installments[1]->id, 'amount' => $installments[1]->debtAmount, 'paid_at' => $this->_carbon]);
        Transaction::factory()->create(['installment_id' => $installments[2]->id, 'amount' => 100000, 'paid_at' => $this->_carbon]);
    }

    #[Test]
    public function successStatistics()
    {
        $this->_carbon = Carbon::now()->subDays(6);

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
        $this->_carbon = Carbon::now()->subDays(6);

        /** @var Branch $branch */
        $branch = Branch::factory()->create();
        Installment::factory()->create(array_merge($this->_input, ['branch_id' => $branch->id]));

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(branch_ids: [$branch->id, 2, 3]),
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
        Installment::factory()->count(5)->create(array_merge($this->_input, ['branch_id' => $branches[1]->id]));
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
