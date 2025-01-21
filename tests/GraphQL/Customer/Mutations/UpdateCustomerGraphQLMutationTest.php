<?php

namespace Tests\GraphQL\Customer\Mutations;

use App\Enums\Company\CompanyStatusEnum;
use App\Enums\GenderEnum;
use App\Models\Company\Customer;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class UpdateCustomerGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private Customer $_customer;

    private array $_customerInput;

    private array $_structure = [
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
    ];

    protected function setUp(): void
    {
        parent::setUp();

        Customer::query()->forceDelete();
        $this->_customer = Customer::factory()->create();

        $this->_customerInput = [
            'pinfl' => rand(10000000000000, 99999999999999),
            'tin' => rand(100000000, 999999999),
            'passport_series' => 'AB',
            'passport_number' => (string) rand(1000000, 9999999),
            'passport_given_date' => now()->format('Y-m-d'),
            'passport_expiration_date' => now()->format('Y-m-d'),
            'passport_given_place' => Str::random(128),
            'passport_citizenship' => Str::random(32),
            'first_name' => Str::random(32),
            'last_name' => Str::random(32),
            'middle_name' => Str::random(32),
            'birth_date' => now()->format('Y-m-d'),
            'birth_place' => Str::random(32),
            'gender' => (int) GenderEnum::randomValue(),
        ];
    }

    #[Test]
    public function successUpdateCustomer()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery([
                    'id' => $this->_customer->id,
                    'customer' => $this->_customerInput,
                ])
            );

        $this->assertSuccessUpdateCustomerResponse($response);
    }

    #[Test]
    public function successUpdateWithDuplicateTin()
    {
        $customer = Customer::factory()->create();
        $this->_customerInput['tin'] = $customer->tin;

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery([
                    'id' => $this->_customer->id,
                    'customer' => $this->_customerInput,
                ])
            );

        $this->assertSuccessUpdateCustomerResponse($response);
        $this->_customer->refresh();
        $this->assertEquals($customer->tin, $this->_customer->tin);
    }

    #[Test]
    public function errorUnique()
    {
        $newCustomer = Customer::factory()->create();

        $this->_customerInput = array_merge($this->_customerInput, [
            'pinfl' => $newCustomer->pinfl,
            'passport_series' => $newCustomer->passport_series,
            'passport_number' => (string) $newCustomer->passport_number,
        ]);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery([
                    'id' => $this->_customer->id,
                    'customer' => $this->_customerInput,
                ])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.unique', ['attribute' => 'customer.pinfl']), $response->json('errors.0.extensions.validation')['customer.pinfl'][0]);
        $this->assertEquals(trans('validation.unique', ['attribute' => 'customer.passport number']), $response->json('errors.0.extensions.validation')['customer.passport_number'][0]);
    }

    #[Test]
    public function errorValidations()
    {
        // required errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery([
                    'id' => null,
                    'customer' => null,
                ])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$id" of non-null type "Int!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$customer" of non-null type "CustomerInputGraphQLType!" must not be null.'));

        // validation errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery([
                    'id' => $this->_customer->id,
                    'customer' => [
                        'pinfl' => (string) rand(100000000000000, 999999999999999),
                        'tin' => rand(1000000000, 9999999999),
                        'passport_series' => '12',
                        'passport_number' => (string) rand(10000000, 99999999),
                        'passport_given_date' => 'passport_given_date',
                        'passport_expiration_date' => 'passport_expiration_date',
                        'passport_given_place' => Str::random(129),
                        'passport_citizenship' => Str::random(33),
                        'first_name' => Str::random(33),
                        'last_name' => Str::random(33),
                        'middle_name' => Str::random(33),
                        'birth_date' => 'birth_date',
                        'birth_place' => Str::random(33),
                        'gender' => 1000,
                    ],
                ])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.digits', ['attribute' => 'customer.pinfl', 'digits' => 14]), $response->json('errors.0.extensions.validation')['customer.pinfl'][0]);
        $this->assertEquals(trans('validation.digits', ['attribute' => 'customer.tin', 'digits' => 9]), $response->json('errors.0.extensions.validation')['customer.tin'][0]);
        $this->assertEquals(trans('validation.regex', ['attribute' => 'customer.passport series']), $response->json('errors.0.extensions.validation')['customer.passport_series'][0]);
        $this->assertEquals(trans('validation.digits', ['attribute' => 'customer.passport number', 'digits' => 7]), $response->json('errors.0.extensions.validation')['customer.passport_number'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'customer.passport given place', 'max' => 128]), $response->json('errors.0.extensions.validation')['customer.passport_given_place'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'customer.passport citizenship', 'max' => 32]), $response->json('errors.0.extensions.validation')['customer.passport_citizenship'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'customer.first name', 'max' => 32]), $response->json('errors.0.extensions.validation')['customer.first_name'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'customer.last name', 'max' => 32]), $response->json('errors.0.extensions.validation')['customer.last_name'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'customer.middle name', 'max' => 32]), $response->json('errors.0.extensions.validation')['customer.middle_name'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'customer.birth place', 'max' => 32]), $response->json('errors.0.extensions.validation')['customer.birth_place'][0]);
        $this->assertEquals(trans('validation.in', ['attribute' => 'customer.gender']), $response->json('errors.0.extensions.validation')['customer.gender'][0]);
        $this->assertEquals(trans('validation.date_format', ['attribute' => 'customer.birth date', 'format' => 'Y-m-d']), $response->json('errors.0.extensions.validation')['customer.birth_date'][0]);
        $this->assertEquals(trans('validation.date_format', ['attribute' => 'customer.passport given date', 'format' => 'Y-m-d']), $response->json('errors.0.extensions.validation')['customer.passport_given_date'][0]);
        $this->assertEquals(trans('validation.date_format', ['attribute' => 'customer.passport expiration date', 'format' => 'Y-m-d']), $response->json('errors.0.extensions.validation')['customer.passport_expiration_date'][0]);
    }

    #[Test]
    public function errorUpdateCustomerWithInactiveCompany(): void
    {
        tenant()->updateOrFail(['status' => CompanyStatusEnum::INACTIVE]);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery(['id' => $this->_customer->id, 'customer' => $this->_customerInput])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals(__('api.inactive_company'), $response->json('errors.0.message'));
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $query['query'] = '
            mutation UpdateCustomer($id: Int!, $customer: CustomerInputGraphQLType!) {
                updateCustomer(id: $id, customer: $customer) {' . $this->convertIntoQuery($this->_structure) . '}
            }
        ';

        return $query;
    }

    private function assertSuccessUpdateCustomerResponse(TestResponse $response): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'updateCustomer' => $this->_structure,
            ],
        ]);

        $this->_customer->refresh();
        $this->assertEquals($this->_customer->pinfl, $this->_customerInput['pinfl']);
        $this->assertEquals($this->_customer->tin, $this->_customerInput['tin']);
        $this->assertEquals($this->_customer->passport_series, $this->_customerInput['passport_series']);
        $this->assertEquals($this->_customer->passport_number, $this->_customerInput['passport_number']);
        $this->assertEquals($this->_customer->passport_given_date?->format('Y-m-d'), $this->_customerInput['passport_given_date']);
        $this->assertEquals($this->_customer->passport_expiration_date?->format('Y-m-d'), $this->_customerInput['passport_expiration_date']);
        $this->assertEquals($this->_customer->passport_given_place, $this->_customerInput['passport_given_place']);
        $this->assertEquals($this->_customer->passport_citizenship, $this->_customerInput['passport_citizenship']);
        $this->assertEquals($this->_customer->first_name, $this->_customerInput['first_name']);
        $this->assertEquals($this->_customer->last_name, $this->_customerInput['last_name']);
        $this->assertEquals($this->_customer->middle_name, $this->_customerInput['middle_name']);
        $this->assertEquals($this->_customer->birth_date?->format('Y-m-d'), $this->_customerInput['birth_date']);
        $this->assertEquals($this->_customer->birth_place, $this->_customerInput['birth_place']);
        $this->assertEquals($this->_customer->gender->value, $this->_customerInput['gender']);

        $this->assertEquals($this->_customer->id, $response->json('data.updateCustomer.id'));
        $this->assertEquals($this->_customer->pinfl, $response->json('data.updateCustomer.pinfl'));
        $this->assertEquals($this->_customer->tin, $response->json('data.updateCustomer.tin'));
        $this->assertEquals($this->_customer->passport_series, $response->json('data.updateCustomer.passport_series'));
        $this->assertEquals($this->_customer->passport_number, $response->json('data.updateCustomer.passport_number'));
        $this->assertEquals($this->_customer->passport_given_date?->timestamp, $response->json('data.updateCustomer.passport_given_date'));
        $this->assertEquals($this->_customer->passport_expiration_date?->timestamp, $response->json('data.updateCustomer.passport_expiration_date'));
        $this->assertEquals($this->_customer->passport_given_place, $response->json('data.updateCustomer.passport_given_place'));
        $this->assertEquals($this->_customer->passport_citizenship, $response->json('data.updateCustomer.passport_citizenship'));
        $this->assertEquals($this->_customer->first_name, $response->json('data.updateCustomer.first_name'));
        $this->assertEquals($this->_customer->last_name, $response->json('data.updateCustomer.last_name'));
        $this->assertEquals($this->_customer->middle_name, $response->json('data.updateCustomer.middle_name'));
        $this->assertEquals($this->_customer->birth_date?->timestamp, $response->json('data.updateCustomer.birth_date'));
        $this->assertEquals($this->_customer->birth_place, $response->json('data.updateCustomer.birth_place'));
        $this->assertEquals($this->_customer->gender->value, $response->json('data.updateCustomer.gender'));
        $this->assertEquals($this->_customer->created_at->timestamp, $response->json('data.updateCustomer.created_at'));
        $this->assertEquals($this->_customer->updated_at->timestamp, $response->json('data.updateCustomer.updated_at'));
        $this->assertEquals($this->_customer->deleted_at?->timestamp, $response->json('data.updateCustomer.deleted_at'));
    }
}
