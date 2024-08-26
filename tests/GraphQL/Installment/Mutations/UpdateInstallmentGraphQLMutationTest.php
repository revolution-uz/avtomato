<?php

namespace Tests\GraphQL\Installment\Mutations;

use App\Enums\Company\InstallmentStatusEnum;
use App\Models\Company\Installment;
use App\Models\Company\Tariff;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class UpdateInstallmentGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private Installment $_installment;

    private Tariff $_tariff;

    protected function setUp(): void
    {
        parent::setUp();

        $this->_installment = Installment::factory()->create(['status' => InstallmentStatusEnum::IN_ACTIVE]);
        $this->_tariff = Tariff::factory()->create([
            'min_period' => 1,
            'max_period' => 10,
            'min_amount' => 1,
            'max_amount' => 10,
            'min_initial_fee' => 1,
            'max_initial_fee' => 10,
        ]);
    }

    #[Test]
    public function successUpdateInstallment()
    {
        $installmentInput = [
            'tariff_id' => $this->_tariff->id,
            'period' => rand($this->_tariff->min_period, $this->_tariff->max_period),
            'amount' => rand($this->_tariff->min_amount, $this->_tariff->max_amount),
            'initial_fee' => rand($this->_tariff->min_initial_fee, $this->_tariff->max_initial_fee),
            'description' => Str::random(10),
            'starts_in' => date('Y-m-d'),
        ];

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery([
                    'id' => $this->_installment->id,
                    'installment' => $installmentInput,
                ])
            );

        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'updateInstallment' => [
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

        $this->_installment->refresh();
        $this->assertEquals($this->_installment->tariff_id, $installmentInput['tariff_id']);
        $this->assertEquals($this->_installment->period, $installmentInput['period']);
        $this->assertEquals($this->_installment->amount, $installmentInput['amount']);
        $this->assertEquals($this->_installment->initial_fee, $installmentInput['initial_fee']);
        $this->assertEquals($this->_installment->description, $installmentInput['description']);
        $this->assertEquals($this->_installment->starts_in->format('Y-m-d'), $installmentInput['starts_in']);

        $this->assertEquals($this->_installment->id, $response->json('data.updateInstallment.id'));
        $this->assertEquals($this->_installment->customer_id, $response->json('data.updateInstallment.customer_id'));
        $this->assertEquals($this->_installment->status->value, $response->json('data.updateInstallment.status'));
        $this->assertEquals($this->_installment->branch_id, $response->json('data.updateInstallment.branch_id'));
        $this->assertEquals($this->_installment->admin_id, $response->json('data.updateInstallment.admin_id'));
        $this->assertEquals($this->_installment->tariff_id, $response->json('data.updateInstallment.tariff_id'));
        $this->assertEquals($this->_installment->type->value, $response->json('data.updateInstallment.type'));
        $this->assertEquals((float) $this->_installment->amount, (float) $response->json('data.updateInstallment.amount'));
        $this->assertEquals((float) $this->_installment->initial_fee, (float) $response->json('data.updateInstallment.initial_fee'));
        $this->assertEquals($this->_installment->sms_notify, $response->json('data.updateInstallment.sms_notify'));
        $this->assertEquals($this->_installment->auto_pay, $response->json('data.updateInstallment.auto_pay'));
        $this->assertEquals($this->_installment->any_card_charge, $response->json('data.updateInstallment.auto_pay_by_pinfl'));
        $this->assertEquals($this->_installment->is_blacklist, $response->json('data.updateInstallment.is_blacklist'));
        $this->assertEquals($this->_installment->period, $response->json('data.updateInstallment.period'));
        $this->assertEquals($this->_installment->description, $response->json('data.updateInstallment.description'));
        $this->assertEquals($this->_installment->cancellation_details, $response->json('data.updateInstallment.cancellation_details'));
        $this->assertEquals($this->_installment->starts_in?->timestamp, $response->json('data.updateInstallment.starts_in'));
        $this->assertEquals($this->_installment->nextPaymentDate?->timestamp, $response->json('data.updateInstallment.next_pay_date'));
        $this->assertEquals($this->_installment->created_at->timestamp, $response->json('data.updateInstallment.created_at'));
        $this->assertEquals($this->_installment->updated_at->timestamp, $response->json('data.updateInstallment.updated_at'));
        $this->assertEquals($this->_installment->canceled_at?->timestamp, $response->json('data.updateInstallment.canceled_at'));
        $this->assertNull($response->json('data.updateInstallment.deleted_at'));
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
                    'installment' => [
                        'tariff_id' => null,
                        'period' => null,
                        'amount' => null,
                        'initial_fee' => null,
                        'description' => null,
                        'starts_in' => null,
                    ],
                ])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$id" of non-null type "Int!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$installment" got invalid value null at "installment.tariff_id"; Expected non-nullable type "Int!" not to be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$installment" got invalid value null at "installment.period"; Expected non-nullable type "Int!" not to be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$installment" got invalid value null at "installment.amount"; Expected non-nullable type "BigFloat!" not to be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$installment" got invalid value null at "installment.initial_fee"; Expected non-nullable type "BigFloat!" not to be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$installment" got invalid value null at "installment.description"; Expected non-nullable type "String!" not to be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$installment" got invalid value null at "installment.starts_in"; Expected non-nullable type "String!" not to be null.'));

        // rule errors
        $installmentInput = [
            'tariff_id' => $this->_tariff->id,
            'period' => $this->_tariff->max_period + 1,
            'amount' => $this->_tariff->max_amount + 1,
            'initial_fee' => $this->_tariff->max_initial_fee + 1,
            'description' => 'qwertyop',
            'starts_in' => 'qwertyop',
        ];

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery([
                    'id' => 999999999,
                    'installment' => $installmentInput,
                ])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'id']), $response->json('errors.0.extensions.validation.id.0'));
        $this->assertEquals("The period must be between {$this->_tariff->min_period} and {$this->_tariff->max_period}.", $response->json('errors.0.extensions.validation')['installment.period'][0]);
        $this->assertEquals("The amount must be between {$this->_tariff->min_amount} and {$this->_tariff->max_amount}.", $response->json('errors.0.extensions.validation')['installment.amount'][0]);
        $this->assertEquals("The initial_fee must be between {$this->_tariff->min_initial_fee} and {$this->_tariff->max_initial_fee}.", $response->json('errors.0.extensions.validation')['installment.initial_fee'][0]);
        $this->assertEquals(trans('validation.date', ['attribute' => 'installment.starts in']), $response->json('errors.0.extensions.validation')['installment.starts_in'][0]);
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $query['query'] = '
            mutation UpdateInstallment(
                $id: Int!
                $installment: InstallmentInputGraphQLType!
            ) {
                updateInstallment(id: $id, installment: $installment) {
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
