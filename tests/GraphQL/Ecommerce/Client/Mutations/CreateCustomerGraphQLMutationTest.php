<?php

namespace Tests\GraphQL\Ecommerce\Client\Mutations;

use App\Enums\GenderEnum;
use App\Models\Company\Customer;
use App\Models\Company\Ecommerce\User;
use Faker\Factory as Faker;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class CreateCustomerGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private User $_user;

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

        $this->faker = Faker::create();
        $this->_user = $this->createEcommerceClientUser();
    }

    #[Test]
    public function successCreateCustomer()
    {
        $variables = $this->getInputVariables();

        $response = $this
            ->actingAs($this->_user, 'ecommerce-client')
            ->postJson(route('graphql.ecommerce-client'), $this->buildGraphQLQuery($variables));

        $this->assertSuccessCreateCustomerResponse($response, $variables);
    }

    #[Test]
    public function successCreateCustomerWithOnlyRequiredAttributes()
    {
        $variables = $this->getInputVariables(true);

        $response = $this
            ->actingAs($this->_user, 'ecommerce-client')
            ->postJson(route('graphql.ecommerce-client'), $this->buildGraphQLQuery($variables));

        $this->assertSuccessCreateCustomerResponse($response, $variables, true);
    }

    #[Test]
    public function successUpdateExistCustomer()
    {
        $this->_user = $this->createEcommerceClientUser(true);
        $variables = $this->getInputVariables(variables: [
            'pinfl' => $this->_user->customer->pinfl,
            'passport_series' => $this->_user->customer->passport_series,
            'passport_number' => $this->_user->customer->passport_number,
            'birth_date' => $this->_user->customer->birth_date->format('Y-m-d'),
            'gender' => $this->_user->customer->gender->value,
            'passport_expiration_date' => $this->_user->customer->passport_expiration_date->format('Y-m-d'),
            'passport_given_date' => $this->_user->customer->passport_given_date->format('Y-m-d'),
        ]);

        $response = $this
            ->actingAs($this->_user, 'ecommerce-client')
            ->postJson(route('graphql.ecommerce-client'), $this->buildGraphQLQuery($variables));

        $this->assertSuccessCreateCustomerResponse($response, $variables);

        $this->_user->refresh();
        $this->assertEquals($this->_user->customer->tin, $variables['tin']);
        $this->assertEquals($this->_user->customer->birth_place, $variables['birth_place']);
        $this->assertEquals($this->_user->customer->passport_given_place, $variables['passport_given_place']);
        $this->assertEquals($this->_user->customer->first_name, $variables['first_name']);
        $this->assertEquals($this->_user->customer->last_name, $variables['last_name']);
        $this->assertEquals($this->_user->customer->middle_name, $variables['middle_name']);
        $this->assertEquals($this->_user->customer->passport_citizenship, $variables['passport_citizenship']);
    }

    #[Test]
    public function errorUnique()
    {
        $variables = $this->getInputVariables();

        // Unique PINFL
        Customer::factory()->create(['pinfl' => $variables['pinfl']]);
        $response = $this
            ->actingAs($this->_user, 'ecommerce-client')
            ->postJson(route('graphql.ecommerce-client'), $this->buildGraphQLQuery($variables));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.unique', ['attribute' => 'customer.pinfl']), $response->json('errors.0.extensions.validation')['customer.pinfl'][0]);

        // Unique passport
        Customer::factory()->create(['passport_series' => $variables['passport_series'], 'passport_number' => $variables['passport_number']]);
        $response = $this
            ->actingAs($this->_user, 'ecommerce-client')
            ->postJson(route('graphql.ecommerce-client'), $this->buildGraphQLQuery($variables));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.unique', ['attribute' => 'customer.passport number']), $response->json('errors.0.extensions.validation')['customer.passport_number'][0]);
    }

    #[Test]
    public function errorCustomerValidation()
    {
        // GraphQL errors
        $response = $this
            ->actingAs($this->_user, 'ecommerce-client')
            ->postJson(route('graphql.ecommerce-client'), $this->buildGraphQLQuery());
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$pinfl" of required type "String!" was not provided.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$passport_series" of required type "String!" was not provided.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$passport_number" of required type "String!" was not provided.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$passport_given_date" of required type "String!" was not provided.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$passport_expiration_date" of required type "String!" was not provided.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$passport_given_place" of required type "String!" was not provided.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$passport_citizenship" of required type "String!" was not provided.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$first_name" of required type "String!" was not provided.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$last_name" of required type "String!" was not provided.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$birth_date" of required type "String!" was not provided.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$birth_place" of required type "String!" was not provided.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$gender" of required type "Int!" was not provided.'));

        // Rule errors
        $variables = $this->getInputVariables(variables: [
            'pinfl' => $this->faker->numerify(str_repeat('#', 13)),
            'tin' => $this->faker->numerify(str_repeat('#', 8)),
            'passport_series' => $this->faker->regexify('[A-Z]{3}'),
            'passport_number' => $this->faker->numerify(str_repeat('#', 8)),
            'passport_given_date' => Str::random(10),
            'passport_expiration_date' => Str::random(10),
            'passport_given_place' => Str::random(129),
            'passport_citizenship' => Str::random(33),
            'first_name' => Str::random(33),
            'last_name' => Str::random(33),
            'middle_name' => Str::random(33),
            'birth_date' => Str::random(10),
            'birth_place' => Str::random(33),
            'gender' => 999,
        ]);
        $response = $this
            ->actingAs($this->_user, 'ecommerce-client')
            ->postJson(route('graphql.ecommerce-client'), $this->buildGraphQLQuery($variables));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.digits', ['attribute' => 'customer.pinfl', 'digits' => 14]), $response->json('errors.0.extensions.validation')['customer.pinfl'][0]);
        $this->assertEquals(trans('validation.digits', ['attribute' => 'customer.tin', 'digits' => 9]), $response->json('errors.0.extensions.validation')['customer.tin'][0]);
        $this->assertEquals(trans('validation.size.string', ['attribute' => 'customer.passport series', 'size' => 2]), $response->json('errors.0.extensions.validation')['customer.passport_series'][0]);
        $this->assertEquals(trans('validation.digits', ['attribute' => 'customer.passport number', 'digits' => 7]), $response->json('errors.0.extensions.validation')['customer.passport_number'][0]);
        $this->assertEquals(trans('validation.date_format', ['attribute' => 'customer.passport given date', 'format' => 'Y-m-d']), $response->json('errors.0.extensions.validation')['customer.passport_given_date'][0]);
        $this->assertEquals(trans('validation.date_format', ['attribute' => 'customer.passport expiration date', 'format' => 'Y-m-d']), $response->json('errors.0.extensions.validation')['customer.passport_expiration_date'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'customer.passport given place', 'max' => 128]), $response->json('errors.0.extensions.validation')['customer.passport_given_place'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'customer.passport citizenship', 'max' => 32]), $response->json('errors.0.extensions.validation')['customer.passport_citizenship'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'customer.first name', 'max' => 32]), $response->json('errors.0.extensions.validation')['customer.first_name'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'customer.last name', 'max' => 32]), $response->json('errors.0.extensions.validation')['customer.last_name'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'customer.middle name', 'max' => 32]), $response->json('errors.0.extensions.validation')['customer.middle_name'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'customer.birth place', 'max' => 32]), $response->json('errors.0.extensions.validation')['customer.birth_place'][0]);
        $this->assertEquals(trans('validation.in', ['attribute' => 'customer.gender']), $response->json('errors.0.extensions.validation')['customer.gender'][0]);
        $this->assertEquals(trans('validation.date_format', ['attribute' => 'customer.birth date', 'format' => 'Y-m-d']), $response->json('errors.0.extensions.validation')['customer.birth_date'][0]);
    }

    protected function buildGraphQLQuery(array $variables = []): array
    {
        return [
            'query' => <<<GRAPHQL
                mutation CreateCustomer(
                    \$pinfl: String!
                    \$tin: BigInteger
                    \$passport_series: String!
                    \$passport_number: String!
                    \$passport_given_date: String!
                    \$passport_expiration_date: String!
                    \$passport_given_place: String!
                    \$passport_citizenship: String!
                    \$first_name: String!
                    \$last_name: String!
                    \$middle_name: String
                    \$birth_date: String!
                    \$birth_place: String!
                    \$gender: Int!
                ) {
                    createCustomer(
                        customer: {
                            pinfl: \$pinfl
                            tin: \$tin
                            passport_series: \$passport_series
                            passport_number: \$passport_number
                            passport_given_date: \$passport_given_date
                            passport_expiration_date: \$passport_expiration_date
                            passport_given_place: \$passport_given_place
                            passport_citizenship: \$passport_citizenship
                            first_name: \$first_name
                            last_name: \$last_name
                            middle_name: \$middle_name
                            birth_date: \$birth_date
                            birth_place: \$birth_place
                            gender: \$gender
                        }
                    ) {
                        {$this->convertIntoQuery($this->_structure)}
                    }
                }
            GRAPHQL,
            'variables' => $variables,
        ];
    }

    private function getInputVariables(bool $isRequired = false, array $variables = [])
    {
        return array_merge(
            [
                'pinfl' => $this->faker->numerify(str_repeat('#', 14)),
                'tin' => ! $isRequired ? $this->faker->numerify(str_repeat('#', 9)) : null,
                'passport_series' => $this->faker->regexify('[A-Z]{2}'),
                'passport_number' => $this->faker->numerify(str_repeat('#', 7)),
                'birth_date' => $this->faker->date,
                'birth_place' => Str::limit($this->faker->city, 32),
                'gender' => $this->faker->randomElement(GenderEnum::values()),
                'first_name' => Str::limit($this->faker->firstName, 32),
                'last_name' => Str::limit($this->faker->lastName, 32),
                'middle_name' => ! $isRequired ? Str::limit($this->faker->firstName, 32) : null,
                'passport_citizenship' => Str::limit($this->faker->country, 32),
                'passport_expiration_date' => $this->faker->date,
                'passport_given_date' => $this->faker->date,
                'passport_given_place' => Str::limit($this->faker->city, 128),
            ],
            $variables
        );
    }

    private function assertSuccessCreateCustomerResponse(TestResponse $response, array $variables, bool $isRequired = false): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure(['data' => ['createCustomer' => $this->_structure]]);

        $this->assertNotNull($this->_user->customer);

        $this->assertEquals($this->_user->customer->pinfl, $variables['pinfl']);
        $this->assertEquals($this->_user->customer->passport_series, $variables['passport_series']);
        $this->assertEquals($this->_user->customer->passport_number, $variables['passport_number']);
        $this->assertEquals($this->_user->customer->birth_date->format('Y-m-d'), $variables['birth_date']);
        $this->assertEquals($this->_user->customer->birth_place, $variables['birth_place']);
        $this->assertEquals($this->_user->customer->first_name, $variables['first_name']);
        $this->assertEquals($this->_user->customer->last_name, $variables['last_name']);
        $this->assertEquals($this->_user->customer->gender->value, $variables['gender']);
        $this->assertEquals($this->_user->customer->passport_citizenship, $variables['passport_citizenship']);
        $this->assertEquals($this->_user->customer->passport_expiration_date->format('Y-m-d'), $variables['passport_expiration_date']);
        $this->assertEquals($this->_user->customer->passport_given_date->format('Y-m-d'), $variables['passport_given_date']);
        $this->assertEquals($this->_user->customer->passport_given_place, $variables['passport_given_place']);
        if ($isRequired) {
            $this->assertNull($this->_user->customer->tin);
            $this->assertNull($this->_user->customer->middle_name);
        } else {
            $this->assertEquals($this->_user->customer->tin, $variables['tin']);
            $this->assertEquals($this->_user->customer->middle_name, $variables['middle_name']);
        }

        $this->assertEquals($this->_user->customer->id, $response->json('data.createCustomer.id'));
        $this->assertEquals($this->_user->customer->pinfl, $response->json('data.createCustomer.pinfl'));
        $this->assertEquals($this->_user->customer->tin, $response->json('data.createCustomer.tin'));
        $this->assertEquals($this->_user->customer->passport_series, $response->json('data.createCustomer.passport_series'));
        $this->assertEquals($this->_user->customer->passport_number, $response->json('data.createCustomer.passport_number'));
        $this->assertEquals($this->_user->customer->passport_given_date->timestamp, $response->json('data.createCustomer.passport_given_date'));
        $this->assertEquals($this->_user->customer->passport_expiration_date->timestamp, $response->json('data.createCustomer.passport_expiration_date'));
        $this->assertEquals($this->_user->customer->passport_given_place, $response->json('data.createCustomer.passport_given_place'));
        $this->assertEquals($this->_user->customer->passport_citizenship, $response->json('data.createCustomer.passport_citizenship'));
        $this->assertEquals($this->_user->customer->first_name, $response->json('data.createCustomer.first_name'));
        $this->assertEquals($this->_user->customer->last_name, $response->json('data.createCustomer.last_name'));
        $this->assertEquals($this->_user->customer->middle_name, $response->json('data.createCustomer.middle_name'));
        $this->assertEquals($this->_user->customer->birth_date->timestamp, $response->json('data.createCustomer.birth_date'));
        $this->assertEquals($this->_user->customer->birth_place, $response->json('data.createCustomer.birth_place'));
        $this->assertEquals($this->_user->customer->gender->value, $response->json('data.createCustomer.gender'));
        $this->assertEquals($this->_user->customer->created_at->timestamp, $response->json('data.createCustomer.created_at'));
        $this->assertEquals($this->_user->customer->updated_at->timestamp, $response->json('data.createCustomer.updated_at'));
        $this->assertEquals($this->_user->customer->deleted_at?->timestamp, $response->json('data.createCustomer.deleted_at'));
    }
}
