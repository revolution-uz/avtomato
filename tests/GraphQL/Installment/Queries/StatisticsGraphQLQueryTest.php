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

class StatisticsGraphQLQueryTest extends GraphQLTestCase
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
            'starts_in' => Carbon::today(),
            'status' => InstallmentStatusEnum::ACTIVE,
        ];

        /** @var Installment[] $installments */
        $installments = [];

        $installments[] = Installment::factory()->create(array_merge($this->_input, ['starts_in' => Carbon::today()->subMonth()]));
        $installments[] = Installment::factory()->create($this->_input);
        $installments[] = Installment::factory()->create($this->_input);
        $installments[] = Installment::factory()->create(array_merge($this->_input, ['starts_in' => Carbon::today()->subDays(1)]));
        $installments[] = Installment::factory()->create(array_merge($this->_input, ['starts_in' => Carbon::today()->subDays(2)]));
        $installments[] = Installment::factory()->create(array_merge($this->_input, ['starts_in' => Carbon::today()->subDays(3)]));

        Transaction::factory()->create(['installment_id' => $installments[1]->id, 'amount' => $installments[1]->debtAmount, 'paid_at' => Carbon::today()]);
        Transaction::factory()->create(['installment_id' => $installments[1]->id, 'amount' => 100000, 'paid_at' => Carbon::today()]);
        Transaction::factory()->create(['installment_id' => $installments[2]->id, 'amount' => 100000, 'paid_at' => Carbon::today()]);
    }

    #[Test]
    public function successStatisticsForLastMonth()
    {
        $to = Carbon::today()->subMonth();
        $from = (clone $to)->subDays(6);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery($from, $to));

        $expectedData = [
            [
                'date' => $from->timestamp,
                'debt_amount' => 0,
                'paid_amount' => 0,
            ],
            [
                'date' => $from->addDay()->timestamp,
                'debt_amount' => 0,
                'paid_amount' => 0,
            ],
            [
                'date' => $from->addDay()->timestamp,
                'debt_amount' => 0,
                'paid_amount' => 0,
            ],
            [
                'date' => $from->addDay()->timestamp,
                'debt_amount' => 0,
                'paid_amount' => 0,
            ],
            [
                'date' => $from->addDay()->timestamp,
                'debt_amount' => 0,
                'paid_amount' => 0,
            ],
            [
                'date' => $from->addDay()->timestamp,
                'debt_amount' => 0,
                'paid_amount' => 0,
            ],
            [
                'date' => $from->addDay()->timestamp,
                'debt_amount' => 345000,
                'paid_amount' => 0,
            ],
        ];

        $this->assertSuccessResponse($response, $expectedData);
    }

    #[Test]
    public function successStatisticsForLastWeek()
    {
        $to = Carbon::today();
        $from = (clone $to)->subDays(6);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery($from, $to));

        $expectedData = [
            [
                'date' => $from->timestamp,
                'debt_amount' => 0,
                'paid_amount' => 0,
            ],
            [
                'date' => $from->addDay()->timestamp,
                'debt_amount' => 0,
                'paid_amount' => 0,
            ],
            [
                'date' => $from->addDay()->timestamp,
                'debt_amount' => 0,
                'paid_amount' => 0,
            ],
            [
                'date' => $from->addDay()->timestamp,
                'debt_amount' => 345000,
                'paid_amount' => 0,
            ],
            [
                'date' => $from->addDay()->timestamp,
                'debt_amount' => 345000,
                'paid_amount' => 0,
            ],
            [
                'date' => $from->addDay()->timestamp,
                'debt_amount' => 345000,
                'paid_amount' => 0,
            ],
            [
                'date' => $from->addDay()->timestamp,
                'debt_amount' => 590000,
                'paid_amount' => 445000,
            ],
        ];

        $this->assertSuccessResponse($response, $expectedData);
    }

    #[Test]
    public function successStatisticsForNextMonth()
    {
        $to = Carbon::today()->addMonth();
        $from = (clone $to)->subDays(6);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery($from, $to));

        $expectedData = [
            [
                'date' => $from->timestamp,
                'debt_amount' => 0,
                'paid_amount' => 0,
            ],
            [
                'date' => $from->addDay()->timestamp,
                'debt_amount' => 0,
                'paid_amount' => 0,
            ],
            [
                'date' => $from->addDay()->timestamp,
                'debt_amount' => 0,
                'paid_amount' => 0,
            ],
            [
                'date' => $from->addDay()->timestamp,
                'debt_amount' => 345000,
                'paid_amount' => 0,
            ],
            [
                'date' => $from->addDay()->timestamp,
                'debt_amount' => 345000,
                'paid_amount' => 0,
            ],
            [
                'date' => $from->addDay()->timestamp,
                'debt_amount' => 345000,
                'paid_amount' => 0,
            ],
            [
                'date' => $from->addDay()->timestamp,
                'debt_amount' => 935000,
                'paid_amount' => 100000,
            ],
        ];

        $this->assertSuccessResponse($response, $expectedData);
    }

    #[Test]
    public function successStatisticsForOutOfPeriodRanges()
    {
        $to = Carbon::today()->subMonths(2);
        $from = (clone $to)->subDays(6);
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery($from, $to));
        $expectedData = [
            [
                'date' => $from->timestamp,
                'debt_amount' => 0,
                'paid_amount' => 0,
            ],
            [
                'date' => $from->addDay()->timestamp,
                'debt_amount' => 0,
                'paid_amount' => 0,
            ],
            [
                'date' => $from->addDay()->timestamp,
                'debt_amount' => 0,
                'paid_amount' => 0,
            ],
            [
                'date' => $from->addDay()->timestamp,
                'debt_amount' => 0,
                'paid_amount' => 0,
            ],
            [
                'date' => $from->addDay()->timestamp,
                'debt_amount' => 0,
                'paid_amount' => 0,
            ],
            [
                'date' => $from->addDay()->timestamp,
                'debt_amount' => 0,
                'paid_amount' => 0,
            ],
            [
                'date' => $from->addDay()->timestamp,
                'debt_amount' => 0,
                'paid_amount' => 0,
            ],
        ];
        $this->assertSuccessResponse($response, $expectedData);

        $to = Carbon::today()->addMonths(3);
        $from = (clone $to)->subDays(6);
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery($from, $to));
        $expectedData = [
            [
                'date' => $from->timestamp,
                'debt_amount' => 0,
                'paid_amount' => 0,
            ],
            [
                'date' => $from->addDay()->timestamp,
                'debt_amount' => 0,
                'paid_amount' => 0,
            ],
            [
                'date' => $from->addDay()->timestamp,
                'debt_amount' => 0,
                'paid_amount' => 0,
            ],
            [
                'date' => $from->addDay()->timestamp,
                'debt_amount' => 0,
                'paid_amount' => 0,
            ],
            [
                'date' => $from->addDay()->timestamp,
                'debt_amount' => 0,
                'paid_amount' => 0,
            ],
            [
                'date' => $from->addDay()->timestamp,
                'debt_amount' => 0,
                'paid_amount' => 0,
            ],
            [
                'date' => $from->addDay()->timestamp,
                'debt_amount' => 0,
                'paid_amount' => 0,
            ],
        ];
        $this->assertSuccessResponse($response, $expectedData);
    }

    #[Test]
    public function successStatisticsForEndOfMonths()
    {
        /** @var Installment $installment */
        $installment = Installment::factory()->create(array_merge($this->_input, ['starts_in' => '2024-12-31']));
        Transaction::factory()->create(['installment_id' => $installment->id, 'amount' => 862500]);

        $to = Carbon::createFromFormat('Y-m-d', '2025-02-28')->startOfDay();
        $from = (clone $to)->subDays(6);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery($from, $to));

        $expectedData = [
            [
                'date' => $from->timestamp,
                'debt_amount' => 0,
                'paid_amount' => 0,
            ],
            [
                'date' => $from->addDay()->timestamp,
                'debt_amount' => 0,
                'paid_amount' => 0,
            ],
            [
                'date' => $from->addDay()->timestamp,
                'debt_amount' => 0,
                'paid_amount' => 0,
            ],
            [
                'date' => $from->addDay()->timestamp,
                'debt_amount' => 0,
                'paid_amount' => 0,
            ],
            [
                'date' => $from->addDay()->timestamp,
                'debt_amount' => 0,
                'paid_amount' => 0,
            ],
            [
                'date' => $from->addDay()->timestamp,
                'debt_amount' => 0,
                'paid_amount' => 0,
            ],
            [
                'date' => $from->addDay()->timestamp,
                'debt_amount' => 172500,
                'paid_amount' => 172500,
            ],
        ];

        $this->assertSuccessResponse($response, $expectedData);
    }

    #[Test]
    public function successStatisticsWithBranchId()
    {
        $to = Carbon::today();
        $from = (clone $to)->subDays(6);

        /** @var Branch $branch */
        $branch = Branch::factory()->create();
        Installment::factory()->create(array_merge($this->_input, ['branch_id' => $branch->id]));

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery($from, $to, $branch->id));

        $expectedData = [
            [
                'date' => $from->timestamp,
                'debt_amount' => 0,
                'paid_amount' => 0,
            ],
            [
                'date' => $from->addDay()->timestamp,
                'debt_amount' => 0,
                'paid_amount' => 0,
            ],
            [
                'date' => $from->addDay()->timestamp,
                'debt_amount' => 0,
                'paid_amount' => 0,
            ],
            [
                'date' => $from->addDay()->timestamp,
                'debt_amount' => 0,
                'paid_amount' => 0,
            ],
            [
                'date' => $from->addDay()->timestamp,
                'debt_amount' => 0,
                'paid_amount' => 0,
            ],
            [
                'date' => $from->addDay()->timestamp,
                'debt_amount' => 0,
                'paid_amount' => 0,
            ],
            [
                'date' => $from->addDay()->timestamp,
                'debt_amount' => 345000,
                'paid_amount' => 0,
            ],
        ];

        $this->assertSuccessResponse($response, $expectedData);
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
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$from" of non-null type "String!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$to" of non-null type "String!" must not be null.'));

        // validation errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery('uytrtdyfgui', 'oytresdtfygiuh', 999999999)
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(__('validation.date_format', ['attribute' => 'from', 'format' => 'Y-m-d']), $response->json('errors.0.extensions.validation.from.0'));
        $this->assertEquals(__('validation.date_format', ['attribute' => 'to', 'format' => 'Y-m-d']), $response->json('errors.0.extensions.validation.to.0'));
        $this->assertEquals(__('validation.exists', ['attribute' => 'branch id']), $response->json('errors.0.extensions.validation.branch_id.0'));
    }

    #[Test]
    public function successCheckForPermission()
    {
        $date = Carbon::today();

        /** @var Role $role */
        $role = Role::factory()->create();
        $simpleAdmin = $this->createSimpleAdminUser($role->id);
        $permission = Permission::query()->where('slug', PermissionEnum::INSTALLMENT_VIEW_STATISTICS)->firstOrFail();

        $branches = Branch::factory()->count(2)->create();
        Installment::factory()->count(5)->create(array_merge($this->_input, ['branch_id' => $branches[1]->id]));

        $role->permissions()->attach($permission, ['branch_id' => $branches[0]->id]);
        $response = $this
            ->actingAs($simpleAdmin)
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery($date, $date));
        $this->assertSuccessResponse($response, [
            [
                'date' => $date->timestamp,
                'debt_amount' => 0,
                'paid_amount' => 0,
            ],
        ]);

        $role->permissions()->attach($permission, ['branch_id' => $branches[1]->id]);
        $response = $this
            ->actingAs($simpleAdmin)
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery($date, $date));
        $this->assertSuccessResponse($response, [
            [
                'date' => $date->timestamp,
                'debt_amount' => 1725000,
                'paid_amount' => 0,
            ],
        ]);
    }

    private function buildGraphQLQuery(string|Carbon|null $from, string|Carbon|null $to, $branch_id = null): array
    {
        if ($from instanceof Carbon) {
            $from = $from->format('Y-m-d');
        }

        if ($to instanceof Carbon) {
            $to = $to->format('Y-m-d');
        }

        $query = [];
        $query['variables'] = ['from' => $from, 'to' => $to, 'branch_id' => $branch_id];
        $query['query'] = '
            query Statistics($from: String!, $to: String!, $branch_id: Int) {
                statistics(from: $from, to: $to, branch_id: $branch_id) {
                    date
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
                'statistics' => [
                    '*' => [
                        'date',
                        'debt_amount',
                        'paid_amount',
                    ],
                ],
            ],
        ]);

        if ($expectedData) {
            $response->assertJson([
                'data' => [
                    'statistics' => $expectedData,
                ],
            ]);
        }
    }
}
