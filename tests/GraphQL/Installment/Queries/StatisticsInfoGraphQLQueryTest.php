<?php

namespace Tests\GraphQL\Installment\Queries;

use App\Enums\Company\InstallmentStatusEnum;
use App\Enums\Company\Rbac\PermissionEnum;
use App\Models\Company\Branch;
use App\Models\Company\Installment;
use App\Models\Company\Rbac\Permission;
use App\Models\Company\Rbac\Role;
use App\Models\Company\Transaction;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class StatisticsInfoGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private array $_input;

    protected function setUp(): void
    {
        parent::setUp();

        Installment::query()->forceDelete();

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

        Transaction::factory()->create(['installment_id' => $installments[1]->id, 'amount' => $installments[1]->debtAmount, 'paid_at' => Carbon::now()]);
        Transaction::factory()->create(['installment_id' => $installments[1]->id, 'amount' => 100000, 'paid_at' => Carbon::now()]);
        Transaction::factory()->create(['installment_id' => $installments[2]->id, 'amount' => 100000, 'paid_at' => Carbon::now()]);
    }

    #[Test]
    public function successStatisticsInfoForLastMonth()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(Carbon::now()->subMonth()));

        $expectedData = [
            [
                'id' => 1,
                'debt_amount' => 345000,
                'paid_amount' => 0,
            ],
        ];

        $this->assertSuccessResponse($response, $expectedData);
    }

    #[Test]
    public function successStatisticsInfoForToday()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(Carbon::now()));

        $expectedData = [
            [
                'id' => 1,
                'debt_amount' => 345000,
                'paid_amount' => 0,
            ],
            [
                'id' => 2,
                'debt_amount' => 0,
                'paid_amount' => 345000,
            ],
            [
                'id' => 3,
                'debt_amount' => 245000,
                'paid_amount' => 100000,
            ],
        ];

        $this->assertSuccessResponse($response, $expectedData);
    }

    #[Test]
    public function successStatisticsInfoForNextMonth()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(Carbon::now()->addMonth()));

        $expectedData = [
            [
                'id' => 1,
                'debt_amount' => 345000,
                'paid_amount' => 0,
            ],
            [
                'id' => 2,
                'debt_amount' => 245000,
                'paid_amount' => 100000,
            ],
            [
                'id' => 3,
                'debt_amount' => 345000,
                'paid_amount' => 0,
            ],
        ];

        $this->assertSuccessResponse($response, $expectedData);
    }

    #[Test]
    public function successStatisticsInfoForOutOfPeriodRanges()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(Carbon::now()->subMonths(2)));
        $this->assertSuccessResponse($response);
        $this->assertEmpty($response->json('data.statisticsInfo'));

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(Carbon::now()->addMonths(3)));
        $this->assertSuccessResponse($response);
        $this->assertEmpty($response->json('data.statisticsInfo'));
    }

    #[Test]
    public function successStatisticsInfoForEndOfMonths()
    {
        /** @var Installment $installment */
        $installment = Installment::factory()->create(array_merge($this->_input, ['starts_in' => '2024-12-31']));
        Transaction::factory()->create(['installment_id' => $installment->id, 'amount' => 862500]);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery('2025-02-28'));

        $this->assertSuccessResponse($response);
        $this->assertEquals(1, count($response->json('data.statisticsInfo')));
        $this->assertEquals($installment->id, $response->json('data.statisticsInfo.0.id'));
        $this->assertEquals(172500, $response->json('data.statisticsInfo.0.paid_amount'));
    }

    #[Test]
    public function successStatisticsInfoWithBranches()
    {
        /** @var Branch $branch */
        $branch = Branch::factory()->create();
        Installment::factory()->create(array_merge($this->_input, ['branch_id' => $branch->id]));

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(Carbon::now(), $branch->id));

        $this->assertSuccessResponse($response);
        $this->assertEquals(1, count($response->json('data.statisticsInfo')));
        $this->assertEquals($branch->name, $response->json('data.statisticsInfo.0.branch_name'));
    }

    #[Test]
    public function errorValidations()
    {
        // graphql query errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(null, null));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$date" of non-null type "String!" must not be null.'));

        // validation errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery('oytresdtfygiuh', 999999999));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(__('validation.date_format', ['attribute' => 'date', 'format' => 'Y-m-d']), $response->json('errors.0.extensions.validation.date.0'));
        $this->assertEquals(__('validation.exists', ['attribute' => 'branches.0']), $response->json('errors.0.extensions.validation')['branches.0'][0]);
    }

    #[Test]
    public function successCheckForPermission()
    {
        /** @var Role $role */
        $role = Role::factory()->create();
        $simpleAdmin = $this->createSimpleAdminUser($role->id);
        $permission = Permission::query()->where('slug', PermissionEnum::INSTALLMENT_VIEW_STATISTICS)->firstOrFail();

        $branches = Branch::factory()->count(2)->create();
        $installments = Installment::factory()->count(5)->create(array_merge($this->_input, ['branch_id' => $branches[1]->id]));

        $role->permissions()->attach($permission, ['branch_id' => $branches[0]->id]);
        $response = $this
            ->actingAs($simpleAdmin)
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(Carbon::now()));
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'statisticsInfo',
            ],
        ]);
        $this->assertEmpty($response->json('data.statisticsInfo'));

        $role->permissions()->attach($permission, ['branch_id' => $branches[1]->id]);
        $response = $this
            ->actingAs($simpleAdmin)
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(Carbon::now()));
        $this->assertSuccessResponse($response);
        $this->assertEquals($installments->pluck('id')?->toArray(), $response->json('data.statisticsInfo.*.id'));
    }

    private function buildGraphQLQuery(string|Carbon|null $date, $branches = null): array
    {
        if ($date instanceof Carbon) {
            $date = $date->format('Y-m-d');
        }

        $query = [];
        $query['variables'] = ['date' => $date, 'branches' => $branches];
        $query['query'] = '
            query StatisticsInfo($date: String!, $branches: [Int!]) {
                statisticsInfo(date: $date, branches: $branches) {
                    id
                    branch_name
                    customer_fullname
                    debt_amount
                    paid_amount
                }
            }
        ';

        return $query;
    }

    private function assertSuccessResponse(TestResponse $response, ?array $expectedData = null): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'statisticsInfo' => [
                    '*' => [
                        'id',
                        'branch_name',
                        'customer_fullname',
                        'debt_amount',
                        'paid_amount',
                    ],
                ],
            ],
        ]);

        if ($expectedData) {
            $response->assertJson([
                'data' => [
                    'statisticsInfo' => $expectedData,
                ],
            ]);
        }
    }
}
