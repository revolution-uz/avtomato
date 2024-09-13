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
                $this->buildGraphQLQuery(['from' => $this->_carbon->format('Y-m-d'), 'to' => Carbon::now()->format('Y-m-d')])
            );

        $expectedData = [
            [
                'date' => $this->_carbon->format('Y-m-d'),
                'debt_amount' => 0,
                'paid_amount' => 0,
            ],
            [
                'date' => $this->_carbon->addDay()->format('Y-m-d'),
                'debt_amount' => 0,
                'paid_amount' => 0,
            ],
            [
                'date' => $this->_carbon->addDay()->format('Y-m-d'),
                'debt_amount' => 0,
                'paid_amount' => 0,
            ],
            [
                'date' => $this->_carbon->addDay()->format('Y-m-d'),
                'debt_amount' => 345000,
                'paid_amount' => 0,
            ],
            [
                'date' => $this->_carbon->addDay()->format('Y-m-d'),
                'debt_amount' => 345000,
                'paid_amount' => 0,
            ],
            [
                'date' => $this->_carbon->addDay()->format('Y-m-d'),
                'debt_amount' => 345000,
                'paid_amount' => 0,
            ],
            [
                'date' => $this->_carbon->addDay()->format('Y-m-d'),
                'debt_amount' => 245000,
                'paid_amount' => 445000,
            ],
        ];

        $this->assertSuccessResponse($response, $expectedData);
    }

    // [Test]
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
                $this->buildGraphQLQuery(['from' => $this->_carbon->format('Y-m-d'), 'to' => Carbon::now()->format('Y-m-d'), 'branch_id' => $branch->id])
            );

        $expectedData = [
            [
                'date' => $this->_carbon->format('Y-m-d'),
                'debt_amount' => 0,
                'paid_amount' => 0,
            ],
            [
                'date' => $this->_carbon->addDay()->format('Y-m-d'),
                'debt_amount' => 0,
                'paid_amount' => 0,
            ],
            [
                'date' => $this->_carbon->addDay()->format('Y-m-d'),
                'debt_amount' => 0,
                'paid_amount' => 0,
            ],
            [
                'date' => $this->_carbon->addDay()->format('Y-m-d'),
                'debt_amount' => 0,
                'paid_amount' => 0,
            ],
            [
                'date' => $this->_carbon->addDay()->format('Y-m-d'),
                'debt_amount' => 0,
                'paid_amount' => 0,
            ],
            [
                'date' => $this->_carbon->addDay()->format('Y-m-d'),
                'debt_amount' => 0,
                'paid_amount' => 0,
            ],
            [
                'date' => $this->_carbon->addDay()->format('Y-m-d'),
                'debt_amount' => 345000,
                'paid_amount' => 0,
            ],
        ];

        $this->assertSuccessResponse($response, $expectedData);
    }

    // [Test]
    public function errorValidations()
    {
        // graphql query errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['from' => null, 'to' => null]));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$from" of non-null type "String!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$to" of non-null type "String!" must not be null.'));

        // validation errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery([
                    'from' => 'uytrtdyfgui',
                    'to' => 'oytresdtfygiuh',
                    'branch_id' => 999999999,
                ])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(__('validation.date_format', ['attribute' => 'from', 'format' => 'Y-m-d']), $response->json('errors.0.extensions.validation.from.0'));
        $this->assertEquals(__('validation.date_format', ['attribute' => 'to', 'format' => 'Y-m-d']), $response->json('errors.0.extensions.validation.to.0'));
        $this->assertEquals(__('validation.exists', ['attribute' => 'branch id']), $response->json('errors.0.extensions.validation.branch_id.0'));
    }

    // [Test]
    public function successCheckForPermission()
    {
        /** @var Role $role */
        $role = Role::factory()->create();
        $simpleAdmin = $this->createSimpleAdminUser($role->id);
        $permission = Permission::query()->where('slug', PermissionEnum::INSTALLMENT_VIEW_STATISTICS)->firstOrFail();

        $branches = Branch::factory()->count(2)->create();
        Installment::factory()->count(5)->create(array_merge($this->_input, ['branch_id' => $branches[1]->id]));

        $role->permissions()->attach($permission, ['branch_id' => $branches[0]->id]);
        $response = $this
            ->actingAs($simpleAdmin)
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(['from' => $this->_carbon->format('Y-m-d'), 'to' => $this->_carbon->format('Y-m-d')])
            );
        $this->assertSuccessResponse($response, [
            [
                'date' => $this->_carbon->format('Y-m-d'),
                'debt_amount' => 0,
                'paid_amount' => 0,
            ],
        ]);

        $role->permissions()->attach($permission, ['branch_id' => $branches[1]->id]);
        $response = $this
            ->actingAs($simpleAdmin)
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(['from' => $this->_carbon->format('Y-m-d'), 'to' => $this->_carbon->format('Y-m-d')])
            );
        $this->assertSuccessResponse($response, [
            [
                'date' => $this->_carbon->format('Y-m-d'),
                'debt_amount' => 1725000,
                'paid_amount' => 0,
            ],
        ]);
    }

    private function buildGraphQLQuery(array $variables): array
    {
        $query = [];
        $query['variables'] = $variables;
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
