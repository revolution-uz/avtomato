<?php

namespace Installment\Mutations;

use App\Models\Company\Customer;
use App\Models\Company\Installment;
use App\Models\Company\InstallmentGuarantor;
use Illuminate\Support\Arr;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class UpdateGuarantorGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    protected function setUp(): void
    {
        parent::setUp();

        Installment::factory()->count(5)->create();
        foreach (Installment::all() as $installment) {
            $installment->guarantors()->attach(Customer::factory()->count(5)->create(), ['any_card_charge' => true]);
        }
    }

    #[Test]
    public function successUpdateGuarantor()
    {
        $installmentGuarantor = InstallmentGuarantor::query()->inRandomOrder()->first();

        $input = [
            'installment_id' => $installmentGuarantor->installment_id,
            'guarantor_id' => $installmentGuarantor->customer_id,
            'any_card_charge' => false,
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
                'updateGuarantor' => [
                    'id',
                    'installment_id',
                    'customer_id',
                    'any_card_charge',
                    'created_at',
                    'updated_at',
                    'deleted_at',
                ],
            ],
        ]);

        $installmentGuarantor->refresh();
        $this->assertEquals($installmentGuarantor->comment, $input['any_card_charge']);

        $this->assertEquals($installmentGuarantor->id, $response->json('data.updateGuarantor.id'));
        $this->assertEquals($installmentGuarantor->installment_id, $response->json('data.updateGuarantor.installment_id'));
        $this->assertEquals($installmentGuarantor->customer_id, $response->json('data.updateGuarantor.customer_id'));
        $this->assertEquals($installmentGuarantor->any_card_charge, $response->json('data.updateGuarantor.any_card_charge'));
        $this->assertEquals($installmentGuarantor->created_at->timestamp, $response->json('data.updateGuarantor.created_at'));
        $this->assertEquals($installmentGuarantor->updated_at->timestamp, $response->json('data.updateGuarantor.updated_at'));
        $this->assertNull($response->json('data.updateGuarantor.deleted_at'));
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
                    'installment_id' => null,
                    'guarantor_id' => null,
                    'any_card_charge' => null,
                ])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$installment_id" of non-null type "Int!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$guarantor_id" of non-null type "Int!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$any_card_charge" of non-null type "Boolean!" must not be null.'));

        // rule errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.installment'),
                $this->buildGraphQLQuery([
                    'installment_id' => 999999999,
                    'guarantor_id' => 999999999,
                    'any_card_charge' => false,
                ])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'installment id']), $response->json('errors.0.extensions.validation.installment_id.0'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'guarantor id']), $response->json('errors.0.extensions.validation.guarantor_id.0'));
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $query['query'] = '
            mutation UpdateGuarantor(
                $installment_id: Int!
                $guarantor_id: Int!
                $any_card_charge: Boolean!
            ) {
                updateGuarantor(
                    installment_id: $installment_id
                    guarantor_id: $guarantor_id
                    any_card_charge: $any_card_charge
                ) {
                    id
                    installment_id
                    customer_id
                    any_card_charge
                    created_at
                    updated_at
                    deleted_at
                }
            }
        ';

        return $query;
    }
}
