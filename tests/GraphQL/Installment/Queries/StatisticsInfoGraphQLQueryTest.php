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
    private Carbon $_carbon;
    private array $_structure = [
        'id',
        'debt',
        'paid',
        'customer' => [
            'id',
            'pinfl',
            'tin',
            'passport_series',
            'passport_number',
            'passport_given_date',
            'passport_expiration_date',
            'passport_given_place',
            'passport_citizenship',
            'first_name',
            'last_name',
            'middle_name',
            'birth_date',
            'birth_place',
            'gender',
            'created_at',
            'updated_at',
            'deleted_at',
        ],
        'branch' => [
            'id',
            'name',
            'address',
            'inn',
            'created_at',
            'updated_at',
            'deleted_at',
        ],
        'transaction' => [
            'id',
            'branch_id',
            'admin_id',
            'installment_id',
            'customer_id',
            'transaction_type_id',
            'card_id',
            'card_pan',
            'external_id',
            'amount',
            'paid_at',
            'created_at',
            'updated_at',
        ],
    ];

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

        $this->_carbon = Carbon::now();

        Transaction::factory()->create(['installment_id' => $installments[1]->id, 'amount' => $installments[1]->debtAmount, 'paid_at' => $this->_carbon]);
        Transaction::factory()->create(['installment_id' => $installments[2]->id, 'amount' => 100000, 'paid_at' => $this->_carbon]);
    }

    #[Test]
    public function successStatisticsInfo()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'), $this->buildGraphQLQuery(['date' => Carbon::now()->format('Y-m-d')])
            );

        $expectedData = [
            [
                'debt' => 690000,
                'paid' => 0,
                'transaction' => null,
            ],
            [
                'debt' => 0,
                'paid' => '345000.00',
                'transaction' => [
                    'amount' => '345000.00',
                    'paid_at' => $this->_carbon->timestamp,
                ],
            ],
            [
                'debt' => 245000,
                'paid' => '100000.00',
                'transaction' => [
                    'amount' => '100000.00',
                    'paid_at' => $this->_carbon->timestamp,
                ],
            ],
        ];

        $this->assertSuccessResponse($response, $expectedData);
    }

    #[Test]
    public function successStatisticsInfoWithBranchId()
    {
        /** @var Branch $branch */
        $branch = Branch::factory()->create();
        Installment::factory()->create(array_merge($this->_input, ['branch_id' => $branch->id]));

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(['date' => Carbon::now()->format('Y-m-d'), 'branch_id' => $branch->id])
            );

        $this->assertSuccessResponse($response);
        $this->assertEquals(1, count($response->json('data.statisticsInfo')));
        $this->assertEquals($branch->id, $response->json('data.statisticsInfo.0.branch.id'));
    }

    #[Test]
    public function errorValidations()
    {
        // graphql query errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(['date' => null, 'branch_id' => null])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$date" of non-null type "String!" must not be null.'));

        // validation errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(['date' => 'oytresdtfygiuh', 'branch_id' => 999999999])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(__('validation.date_format', ['attribute' => 'date', 'format' => 'Y-m-d']), $response->json('errors.0.extensions.validation.date.0'));
        $this->assertEquals(__('validation.exists', ['attribute' => 'branch id']), $response->json('errors.0.extensions.validation.branch_id.0'));
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
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['date' => Carbon::now()->format('Y-m-d')]));
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
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['date' => Carbon::now()->format('Y-m-d')]));
        $this->assertSuccessResponse($response);
        $this->assertEquals($installments->pluck('id')?->toArray(), $response->json('data.statisticsInfo.*.id'));
    }

    private function buildGraphQLQuery(array $variables): array
    {
        $query = [];
        $query['variables'] = $variables;
        $query['query'] = '
            query StatisticsInfo($date: String!, $branch_id: Int) {
                statisticsInfo(date: $date, branch_id: $branch_id) {
                    ' . $this->convertIntoQuery($this->_structure) . '
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
                    '*' => Arr::except($this->_structure, 'transaction'),
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
