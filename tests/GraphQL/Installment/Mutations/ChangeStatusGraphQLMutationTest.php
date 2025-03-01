<?php

namespace Tests\GraphQL\Installment\Mutations;

use App\Enums\Company\InstallmentStatusEnum;
use App\Enums\Company\Rbac\PermissionEnum;
use App\Models\Company\Branch;
use App\Models\Company\Installment;
use App\Models\Company\Rbac\Permission;
use App\Models\Company\Rbac\Role;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class ChangeStatusGraphQLMutationTest extends GraphQLTestCase
{
    private const GRAPHQL_ROUTE = 'graphql.installment';

    protected bool $tenancy = true;

    private $_branch;

    protected function setUp(): void
    {
        parent::setUp();
        Installment::query()->forceDelete();
        $this->_branch = Branch::query()->firstOrFail();
    }

    #[Test]
    public function successChangeStatusActiveToFrozen(): void
    {
        $installment = Installment::factory()->create(
            [
                'status' => InstallmentStatusEnum::ACTIVE,
                'branch_id' => $this->_branch->id,
            ]
        );
        $response = $this->sendGraphQLRequest(
            $installment->id,
            InstallmentStatusEnum::FROZEN->value
        );
        $this->assertSuccessResponse($response);
    }

    #[Test]
    public function successChangeStatusActiveToInActive(): void
    {
        $installment = Installment::factory()->create(
            [
                'status' => InstallmentStatusEnum::ACTIVE,
                'branch_id' => $this->_branch->id,
            ]
        );
        $response = $this->sendGraphQLRequest(
            $installment->id,
            InstallmentStatusEnum::FROZEN->value
        );

        $this->assertSuccessResponse($response);
    }

    #[Test]
    public function errorStatus(): void
    {
        $installment = Installment::factory()->create(
            [
                'status' => InstallmentStatusEnum::CANCELED,
                'branch_id' => $this->_branch->id,
            ]
        );
        $response = $this->sendGraphQLRequest(
            $installment->id,
            InstallmentStatusEnum::CANCELED->value
        );
        $response->assertJsonIsObject();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertStringContainsString('validation', $response->json('errors.0.message'));
        $this->assertEquals(
            'Status is not valid for this installment',
            $response->json('errors.0.extensions.validation')['status'][0]
        );
    }

    #[Test]
    public function errorInstallmentId(): void
    {

        $response = $this->sendGraphQLRequest(123, 1);
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertStringContainsString('validation', $response->json('errors.0.message'));
        $this->assertEquals(
            trans('validation.exists', ['attribute' => 'installment id']),
            $response->json('errors.0.extensions.validation')['installment_id'][0]
        );
    }

    #[Test]
    public function failActivate(): void
    {
        $installment = Installment::factory()->create(['status' => InstallmentStatusEnum::INACTIVE, 'branch_id' => $this->_branch->id]);
        $response = $this->sendGraphQLRequest($installment->id, InstallmentStatusEnum::ACTIVE->value);
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertStringContainsString('validation', $response->json('errors.0.message'));
        $this->assertEquals(
            trans('api.You cannot activate an installment without withdrawing initial fee'),
            $response->json('errors.0.extensions.validation.status.0')
        );
    }

    #[Test]
    public function successCheckForPermission()
    {
        $role = Role::factory()->create();
        $simpleAdmin = $this->createSimpleAdminUser($role->id);
        $permission = Permission::query()->where('slug', PermissionEnum::INSTALLMENT_ATTACH_CARD)->firstOrFail();

        $branches = Branch::factory()->count(2)->create();
        $installment = Installment::factory()->create(['branch_id' => $branches[1]->id, 'status' => InstallmentStatusEnum::ACTIVE]);

        $role->permissions()->attach($permission, ['branch_id' => $branches[0]->id]);
        $response = $this
            ->actingAs($simpleAdmin)
            ->postJson(
                route(self::GRAPHQL_ROUTE),
                $this->buildGraphQLQuery($installment->id, InstallmentStatusEnum::FROZEN->value)
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'installment id']), $response->json('errors.0.extensions.validation.installment_id.0'));

        $role->permissions()->attach($permission, ['branch_id' => $branches[1]->id]);
        $response = $this
            ->actingAs($simpleAdmin)
            ->postJson(
                route(self::GRAPHQL_ROUTE),
                $this->buildGraphQLQuery($installment->id, InstallmentStatusEnum::FROZEN->value)
            );
        $this->assertSuccessResponse($response);
    }

    private function sendGraphQLRequest(int $installment_id, int $status): TestResponse
    {
        return $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route(self::GRAPHQL_ROUTE),
                $this->buildGraphQLQuery($installment_id, $status)
            );
    }

    private function buildGraphQLQuery(int $installment_id, int $status): array
    {
        return [
            'query' => 'mutation ChangeStatus($installment_id: Int!, $status: Int!) {
                changeStatus(installment_id: $installment_id, status: $status){
                    id
                    customer_id
                    customer {
                        id
                        pinfl
                        tin
                        passport_series
                        passport_number
                        passport_given_date
                        passport_expiration_date
                        passport_given_place
                        passport_citizenship
                        first_name
                        last_name
                        middle_name
                        birth_date
                        birth_place
                        gender
                        created_at
                        updated_at
                        deleted_at
                    }
                    status
                    branch_id
                    admin_id
                    tariff_id
                    type
                    amount
                    initial_fee
                    sms_notify
                    auto_pay
                    auto_pay_by_pinfl
                    period
                    description
                    starts_in
                    next_pay_date
                    created_at
                    updated_at
                    canceled_at
                }
            }',
            'variables' => [
                'installment_id' => $installment_id,
                'status' => $status,
            ],
        ];
    }

    private function assertSuccessResponse($response): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure(
            ['data' => [
                'changeStatus' => [
                    'id',
                    'customer_id',
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
                        'gender',
                        'birth_place',
                        'created_at',
                        'updated_at',
                        'deleted_at',
                    ],
                    'status',
                    'branch_id',
                    'admin_id',
                    'tariff_id',
                    'type',
                    'amount',
                    'initial_fee',
                    'sms_notify',
                    'auto_pay',
                    'auto_pay',
                    'auto_pay_by_pinfl',
                    'period',
                    'description',
                    'starts_in',
                    'next_pay_date',
                    'created_at',
                    'updated_at',
                    'canceled_at',
                ],
            ],
            ]);
    }
}
