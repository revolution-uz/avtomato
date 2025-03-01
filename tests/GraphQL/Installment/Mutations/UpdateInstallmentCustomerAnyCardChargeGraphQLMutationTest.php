<?php

namespace Tests\GraphQL\Installment\Mutations;

use App\Enums\Company\InstallmentStatusEnum;
use App\Enums\Company\InstallmentTypeEnum;
use App\Enums\Company\Rbac\PermissionEnum;
use App\Enums\FeatureEnum;
use App\Models\Company\BalanceCharge;
use App\Models\Company\Branch;
use App\Models\Company\Feature;
use App\Models\Company\Installment;
use App\Models\Company\Rbac\Permission;
use App\Models\Company\Rbac\Role;
use Illuminate\Support\Arr;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class UpdateInstallmentCustomerAnyCardChargeGraphQLMutationTest extends GraphQLTestCase
{
    private const array STRUCTURE = [
        'id',
        'customer_id',
        'status',
        'branch_id',
        'admin_id',
        'tariff_id',
        'type',
        'amount',
        'initial_fee',
        'sms_notify',
        'auto_pay',
        'auto_pay_by_pinfl',
        'is_blacklist',
        'period',
        'description',
        'cancellation_details',
        'starts_in',
        'next_pay_date',
        'created_at',
        'updated_at',
        'canceled_at',
        'deleted_at',
    ];

    protected bool $tenancy = true;

    private Installment $_installment;
    private array $_input;
    private Branch $_branch;

    protected function setUp(): void
    {
        parent::setUp();

        $this->_branch = Branch::query()->firstOrFail();
        Installment::factory()->count(5)->create([
            'status' => InstallmentStatusEnum::ACTIVE,
            'any_card_charge' => false,
            'type' => InstallmentTypeEnum::WITHOUT_CARD,
            'branch_id' => $this->_branch->id,
        ]);

        $this->_installment = Installment::query()->inRandomOrder()->first();
        $this->_input = ['id' => $this->_installment->id, 'any_card_charge' => true];
    }

    #[Test]
    public function successUpdateInstallmentCustomerAnyCardCharge()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery($this->_input));

        $this->assertSuccessResponse($response);
    }

    #[Test]
    public function successCheckForCorrectPaymentCases()
    {
        /** @var Feature $feature */
        $feature = Feature::query()->where('name', FeatureEnum::INSTALLMENT_AUTO_CHARGE_BY_PINFL)->firstOrFail();
        $feature->update(['amount' => 29]);

        // when period is equal to 1
        $installment = Installment::factory()->create([
            'period' => 1, 'any_card_charge' => false,
            'type' => InstallmentTypeEnum::WITHOUT_CARD,
            'branch_id' => $this->_branch->id,
        ]);
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['id' => $installment->id, 'any_card_charge' => true]));
        $this->assertCorrectPayment($response, $feature, $installment, 29);

        // when period is equal to 3
        $installment = Installment::factory()->create([
            'period' => 3,
            'any_card_charge' => false,
            'type' => InstallmentTypeEnum::WITH_CARD,
            'branch_id' => $this->_branch->id,
        ]);
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['id' => $installment->id, 'any_card_charge' => true]));
        $this->assertCorrectPayment($response, $feature, $installment, 29);

        // when period is equal to 4
        $installment = Installment::factory()->create([
            'period' => 4,
            'any_card_charge' => false,
            'type' => InstallmentTypeEnum::WITHOUT_CARD,
            'branch_id' => $this->_branch->id,
        ]);
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['id' => $installment->id, 'any_card_charge' => true]));
        $this->assertCorrectPayment($response, $feature, $installment, 58);
    }

    #[Test]
    public function successCheckNonPaymentCases()
    {
        /** @var Feature $feature */
        $feature = Feature::query()->where('name', FeatureEnum::INSTALLMENT_AUTO_CHARGE_BY_PINFL)->firstOrFail();
        $feature->update(['amount' => 29]);

        // when any_card_charge is already true
        $installment = Installment::factory()->create([
            'any_card_charge' => true,
            'type' => InstallmentTypeEnum::WITHOUT_CARD,
            'branch_id' => $this->_branch->id,
        ]);
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['id' => $installment->id, 'any_card_charge' => true]));
        $this->assertNonPayment($response, $feature, $installment);

        // when type is already WITH_PINFL
        $installment = Installment::factory()->create([
            'any_card_charge' => false,
            'type' => InstallmentTypeEnum::WITH_PINFL,
            'branch_id' => $this->_branch->id,
        ]);
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['id' => $installment->id, 'any_card_charge' => true]));
        $this->assertNonPayment($response, $feature, $installment);

        // when type is already WITH_BOTH
        $installment = Installment::factory()->create([
            'any_card_charge' => true,
            'type' => InstallmentTypeEnum::WITH_BOTH,
            'branch_id' => $this->_branch->id,
        ]);
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery(['id' => $installment->id, 'any_card_charge' => true]));
        $this->assertNonPayment($response, $feature, $installment);
    }

    #[Test]
    public function errorValidation(): void
    {
        // GraphQL type errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(['id' => null, 'any_card_charge' => null])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$id" of non-null type "Int!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$any_card_charge" of non-null type "Boolean!" must not be null.'));

        // rule errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery(['id' => 999999999, 'any_card_charge' => true])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'id']), $response->json('errors.0.extensions.validation.id.0'));
    }

    #[Test]
    public function successCheckForPermission()
    {
        $role = Role::factory()->create();
        $simpleAdmin = $this->createSimpleAdminUser($role->id);
        $permission = Permission::query()->where('slug', PermissionEnum::INSTALLMENT_EDIT)->firstOrFail();

        $branches = Branch::factory()->count(2)->create();
        $this->_installment = Installment::factory()->create(['branch_id' => $branches[1]->id, 'status' => InstallmentStatusEnum::ACTIVE, 'any_card_charge' => false, 'type' => InstallmentTypeEnum::WITHOUT_CARD]);
        $this->_input = array_merge($this->_input, ['id' => $this->_installment->id]);

        $role->permissions()->attach($permission, ['branch_id' => $branches[0]->id]);
        $response = $this
            ->actingAs($simpleAdmin)
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery($this->_input));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals(__('validation.exists', ['attribute' => 'id']), $response->json('errors.0.extensions.validation.id.0'));

        $role->permissions()->attach($permission, ['branch_id' => $branches[1]->id]);
        $response = $this
            ->actingAs($simpleAdmin)
            ->postJson(route('graphql.installment'), $this->buildGraphQLQuery($this->_input));
        $this->assertSuccessResponse($response);
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $query['query'] = '
            mutation UpdateInstallmentCustomerAnyCardCharge(
                $id: Int!
                $any_card_charge: Boolean!
            ) {
                updateInstallmentCustomerAnyCardCharge(
                    id: $id
                    any_card_charge: $any_card_charge
                ) {' . $this->convertIntoQuery(self::STRUCTURE) . '}
            }
        ';

        return $query;
    }

    private function assertSuccessResponse(TestResponse $response): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure(['data' => ['updateInstallmentCustomerAnyCardCharge' => self::STRUCTURE]]);

        $this->_installment->refresh();
        $this->assertEquals($this->_installment->any_card_charge, $this->_input['any_card_charge']);

        $this->assertEquals($this->_installment->id, $response->json('data.updateInstallmentCustomerAnyCardCharge.id'));
        $this->assertEquals($this->_installment->customer_id, $response->json('data.updateInstallmentCustomerAnyCardCharge.customer_id'));
        $this->assertEquals($this->_installment->status->value, $response->json('data.updateInstallmentCustomerAnyCardCharge.status'));
        $this->assertEquals($this->_installment->branch_id, $response->json('data.updateInstallmentCustomerAnyCardCharge.branch_id'));
        $this->assertEquals($this->_installment->admin_id, $response->json('data.updateInstallmentCustomerAnyCardCharge.admin_id'));
        $this->assertEquals($this->_installment->tariff_id, $response->json('data.updateInstallmentCustomerAnyCardCharge.tariff_id'));
        $this->assertEquals($this->_installment->type->value, $response->json('data.updateInstallmentCustomerAnyCardCharge.type'));
        $this->assertEquals($this->_installment->amount, $response->json('data.updateInstallmentCustomerAnyCardCharge.amount'));
        $this->assertEquals($this->_installment->initial_fee, $response->json('data.updateInstallmentCustomerAnyCardCharge.initial_fee'));
        $this->assertEquals($this->_installment->sms_notify, $response->json('data.updateInstallmentCustomerAnyCardCharge.sms_notify'));
        $this->assertEquals($this->_installment->auto_pay, $response->json('data.updateInstallmentCustomerAnyCardCharge.auto_pay'));
        $this->assertEquals($this->_installment->any_card_charge, $response->json('data.updateInstallmentCustomerAnyCardCharge.auto_pay_by_pinfl'));
        $this->assertEquals($this->_installment->is_blacklist, $response->json('data.updateInstallmentCustomerAnyCardCharge.is_blacklist'));
        $this->assertEquals($this->_installment->period, $response->json('data.updateInstallmentCustomerAnyCardCharge.period'));
        $this->assertEquals($this->_installment->description, $response->json('data.updateInstallmentCustomerAnyCardCharge.description'));
        $this->assertEquals($this->_installment->cancellation_details, $response->json('data.updateInstallmentCustomerAnyCardCharge.cancellation_details'));
        $this->assertEquals($this->_installment->starts_in?->timestamp, $response->json('data.updateInstallmentCustomerAnyCardCharge.starts_in'));
        $this->assertEquals($this->_installment->nextPaymentDate?->timestamp, $response->json('data.updateInstallmentCustomerAnyCardCharge.next_pay_date'));
        $this->assertEquals($this->_installment->created_at->timestamp, $response->json('data.updateInstallmentCustomerAnyCardCharge.created_at'));
        $this->assertEquals($this->_installment->updated_at->timestamp, $response->json('data.updateInstallmentCustomerAnyCardCharge.updated_at'));
        $this->assertEquals($this->_installment->canceled_at?->timestamp, $response->json('data.updateInstallmentCustomerAnyCardCharge.canceled_at'));
        $this->assertNull($response->json('data.updateInstallmentCustomerAnyCardCharge.deleted_at'));
    }

    private function assertCorrectPayment(TestResponse $response, Feature $feature, Installment $installment, int $amount): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure(['data' => ['updateInstallmentCustomerAnyCardCharge' => self::STRUCTURE]]);

        /** @var BalanceCharge $charge */
        $charge = $feature->balanceCharges()
            ->where('chargeable_type', Installment::class)
            ->where('chargeable_id', $installment->id)
            ->first();

        $this->assertNotNull($charge);
        $this->assertEquals($amount, $charge->amount);
    }

    private function assertNonPayment(TestResponse $response, Feature $feature, Installment $installment): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure(['data' => ['updateInstallmentCustomerAnyCardCharge' => self::STRUCTURE]]);

        $feature->balanceCharges()
            ->where('chargeable_type', Installment::class)
            ->where('chargeable_id', $installment->id)
            ->doesntExist();
    }
}
