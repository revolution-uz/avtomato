<?php

namespace Installment\Mutations;

use App\Enums\Company\InstallmentStatusEnum;
use App\Models\Company\Installment;
use Illuminate\Support\Arr;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class UpdateInstallmentCustomerAnyCardChargeGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    protected function setUp(): void
    {
        parent::setUp();

        Installment::factory()->count(5)->create([
            'status' => InstallmentStatusEnum::IN_ACTIVE,
            'any_card_charge' => false,
        ]);
    }

    #[Test]
    public function successUpdateInstallmentCustomerAnyCardCharge()
    {
        $installment = Installment::query()->inRandomOrder()->first();

        $input = [
            'id' => $installment->id,
            'any_card_charge' => true,
        ];

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery($input)
            );

        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'updateInstallmentCustomerAnyCardCharge' => [
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
                ],
            ],
        ]);

        $installment->refresh();
        $this->assertEquals($installment->any_card_charge, $input['any_card_charge']);

        $this->assertEquals($installment->id, $response->json('data.updateInstallmentCustomerAnyCardCharge.id'));
        $this->assertEquals($installment->customer_id, $response->json('data.updateInstallmentCustomerAnyCardCharge.customer_id'));
        $this->assertEquals($installment->status->value, $response->json('data.updateInstallmentCustomerAnyCardCharge.status'));
        $this->assertEquals($installment->branch_id, $response->json('data.updateInstallmentCustomerAnyCardCharge.branch_id'));
        $this->assertEquals($installment->admin_id, $response->json('data.updateInstallmentCustomerAnyCardCharge.admin_id'));
        $this->assertEquals($installment->tariff_id, $response->json('data.updateInstallmentCustomerAnyCardCharge.tariff_id'));
        $this->assertEquals($installment->type->value, $response->json('data.updateInstallmentCustomerAnyCardCharge.type'));
        $this->assertEquals($installment->amount, $response->json('data.updateInstallmentCustomerAnyCardCharge.amount'));
        $this->assertEquals($installment->initial_fee, $response->json('data.updateInstallmentCustomerAnyCardCharge.initial_fee'));
        $this->assertEquals($installment->sms_notify, $response->json('data.updateInstallmentCustomerAnyCardCharge.sms_notify'));
        $this->assertEquals($installment->auto_pay, $response->json('data.updateInstallmentCustomerAnyCardCharge.auto_pay'));
        $this->assertEquals($installment->any_card_charge, $response->json('data.updateInstallmentCustomerAnyCardCharge.auto_pay_by_pinfl'));
        $this->assertEquals($installment->is_blacklist, $response->json('data.updateInstallmentCustomerAnyCardCharge.is_blacklist'));
        $this->assertEquals($installment->period, $response->json('data.updateInstallmentCustomerAnyCardCharge.period'));
        $this->assertEquals($installment->description, $response->json('data.updateInstallmentCustomerAnyCardCharge.description'));
        $this->assertEquals($installment->cancellation_details, $response->json('data.updateInstallmentCustomerAnyCardCharge.cancellation_details'));
        $this->assertEquals($installment->starts_in?->timestamp, $response->json('data.updateInstallmentCustomerAnyCardCharge.starts_in'));
        $this->assertEquals($installment->nextPaymentDate?->timestamp, $response->json('data.updateInstallmentCustomerAnyCardCharge.next_pay_date'));
        $this->assertEquals($installment->created_at->timestamp, $response->json('data.updateInstallmentCustomerAnyCardCharge.created_at'));
        $this->assertEquals($installment->updated_at->timestamp, $response->json('data.updateInstallmentCustomerAnyCardCharge.updated_at'));
        $this->assertEquals($installment->canceled_at?->timestamp, $response->json('data.updateInstallmentCustomerAnyCardCharge.canceled_at'));
        $this->assertNull($response->json('data.updateInstallmentCustomerAnyCardCharge.deleted_at'));
    }

    #[Test]
    public function errorValidation(): void
    {
        // GraphQL type errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery([
                    'id' => null,
                    'any_card_charge' => null,
                ])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn($item) => $item['message'] === 'Variable "$id" of non-null type "Int!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn($item) => $item['message'] === 'Variable "$any_card_charge" of non-null type "Boolean!" must not be null.'));

        // rule errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery([
                    'id' => 999999999,
                    'any_card_charge' => true,
                ])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'id']), $response->json('errors.0.extensions.validation.id.0'));
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
                ) {
                    id
                    customer_id
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
                    is_blacklist
                    period
                    description
                    cancellation_details
                    starts_in
                    next_pay_date
                    created_at
                    updated_at
                    canceled_at
                    deleted_at
                }
            }
        ';

        return $query;
    }
}
