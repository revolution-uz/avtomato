<?php

namespace Tests\GraphQL\Installment\Queries;

use App\Models\Company\Installment;
use App\Models\Company\Tariff;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class SingleTariffGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private Tariff $_tariff;

    protected function setUp(): void
    {
        parent::setUp();

        Tariff::query()->forceDelete();
        $this->_tariff = Tariff::factory()->create();
        Installment::factory()->count(5)->create(['tariff_id' => $this->_tariff->id]);
    }

    #[Test]
    public function successSingleTariff()
    {
        $response = $this->sendToGraphQL($this->_tariff->id);
        $this->assertSuccessSingleTariffResponse($response);
    }

    #[Test]
    public function errorSingleTariff()
    {
        // not found
        $response = $this->sendToGraphQL(999);
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'id']), $response->json('errors.0.extensions.validation.id.0'));
        // deleted
        $this->_tariff->delete();
        $response = $this->sendToGraphQL($this->_tariff->id);

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'id']), $response->json('errors.0.extensions.validation.id.0'));
    }

    private function buildGraphQLQuery($id): array
    {
        $query = [];
        $query['variables'] = ['id' => $id];
        $query['query'] = '
            query ($id: Int!) {
                singleTariff(id: $id) {
                    id
                    name
                    is_annual
                    percent
                    min_period
                    max_period
                    min_amount
                    max_amount
                    min_initial_fee
                    max_initial_fee
                    installments {
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
                        debt
                    }
                }
            }
        ';

        return $query;
    }

    private function assertSuccessSingleTariffResponse(TestResponse $response): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'singleTariff' => [
                    'id',
                    'name',
                    'percent',
                    'min_period',
                    'max_period',
                    'min_amount',
                    'max_amount',
                    'min_initial_fee',
                    'max_initial_fee',
                    'installments' => [
                        '*' => [
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
                            'debt',
                        ],
                    ],
                ],
            ],
        ]);
        $tariff = Tariff::query()->find($response->json('data.singleTariff.id'));
        $this->assertNotNull($tariff);
        $this->assertEquals($tariff->id, $response->json('data.singleTariff.id'));
        $this->assertEquals($tariff->name, $response->json('data.singleTariff.name'));
        $this->assertEquals($tariff->percent, $response->json('data.singleTariff.percent'));
        $this->assertEquals($tariff->min_period, $response->json('data.singleTariff.min_period'));
        $this->assertEquals($tariff->max_period, $response->json('data.singleTariff.max_period'));
        $this->assertEquals($tariff->min_amount, $response->json('data.singleTariff.min_amount'));
        $this->assertEquals($tariff->max_amount, $response->json('data.singleTariff.max_amount'));
        $this->assertEquals($tariff->min_initial_fee, $response->json('data.singleTariff.min_initial_fee'));
        $this->assertEquals($tariff->max_initial_fee, $response->json('data.singleTariff.max_initial_fee'));

        $this->assertEquals(5, count($response->json('data.singleTariff.installments')));
        $this->assertEquals($tariff->installments[0]->id, $response->json('data.singleTariff.installments.0.id'));
        $this->assertEquals($tariff->installments[0]->customer_id, $response->json('data.singleTariff.installments.0.customer_id'));
        $this->assertEquals($tariff->installments[0]->status->value, $response->json('data.singleTariff.installments.0.status'));
        $this->assertEquals($tariff->installments[0]->branch_id, $response->json('data.singleTariff.installments.0.branch_id'));
        $this->assertEquals($tariff->installments[0]->admin_id, $response->json('data.singleTariff.installments.0.admin_id'));
        $this->assertEquals($tariff->installments[0]->tariff_id, $response->json('data.singleTariff.installments.0.tariff_id'));
        $this->assertEquals($tariff->installments[0]->type->value, $response->json('data.singleTariff.installments.0.type'));
        $this->assertEquals($tariff->installments[0]->amount, $response->json('data.singleTariff.installments.0.amount'));
        $this->assertEquals($tariff->installments[0]->initial_fee, $response->json('data.singleTariff.installments.0.initial_fee'));
        $this->assertEquals($tariff->installments[0]->sms_notify, $response->json('data.singleTariff.installments.0.sms_notify'));
        $this->assertEquals($tariff->installments[0]->auto_pay, $response->json('data.singleTariff.installments.0.auto_pay'));
        $this->assertEquals($tariff->installments[0]->any_card_charge, $response->json('data.singleTariff.installments.0.auto_pay_by_pinfl'));
        $this->assertEquals($tariff->installments[0]->is_blacklist, $response->json('data.singleTariff.installments.0.is_blacklist'));
        $this->assertEquals($tariff->installments[0]->period, $response->json('data.singleTariff.installments.0.period'));
        $this->assertEquals($tariff->installments[0]->description, $response->json('data.singleTariff.installments.0.description'));
        $this->assertEquals($tariff->installments[0]->cancellation_details, $response->json('data.singleTariff.installments.0.cancellation_details'));
        $this->assertEquals($tariff->installments[0]->starts_in?->timestamp, $response->json('data.singleTariff.installments.0.starts_in'));
        $this->assertEquals($tariff->installments[0]->nextPaymentDate->timestamp, $response->json('data.singleTariff.installments.0.next_pay_date'));
        $this->assertEquals($tariff->installments[0]->created_at->timestamp, $response->json('data.singleTariff.installments.0.created_at'));
        $this->assertEquals($tariff->installments[0]->updated_at->timestamp, $response->json('data.singleTariff.installments.0.updated_at'));
        $this->assertEquals($tariff->installments[0]->canceled_at?->timestamp, $response->json('data.singleTariff.installments.0.canceled_at'));
        $this->assertEquals($tariff->installments[0]->deleted_at?->timestamp, $response->json('data.singleTariff.installments.0.deleted_at'));
        $this->assertEquals($tariff->installments[0]->debtAmount, $response->json('data.singleTariff.installments.0.debt'));
    }

    private function sendToGraphQL($id): TestResponse
    {
        return $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery($id));
    }
}
