<?php

namespace Tests\GraphQL\Customer\Mutations;

use App\Enums\Company\AddressTypeEnum;
use App\Enums\Company\PhoneTypeEnum;
use App\Models\Company\Address;
use App\Models\Company\Card;
use App\Models\Company\Customer;
use App\Models\Company\Phone;
use Database\Seeders\Company\LocationSeeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class CreateCustomerGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private array $_customerInput = [
        'pinfl' => '31503953130027',
        'tin' => '520212090',
        'passport_series' => 'AD',
        'passport_number' => '3119359',
        'birth_date' => '15-03-1995',
        'birth_place' => 'URGANCH SHAHRI',
        'first_name' => 'YO‘LDOSHBOY',
        'gender' => 0,
        'last_name' => 'SULTONOV',
        'middle_name' => 'O‘RAZMETBOYEVICH',
        'passport_citizenship' => 'O‘ZBEKISTON',
        'passport_expiration_date' => '24-04-2033',
        'passport_given_date' => '25-04-2023',
        'passport_given_place' => 'XORAZM VILOYATI YANGIBOZOR TUMANI IIB',
    ];

    private array $_addressInput = [
        'region_id' => 13,
        'district_id' => 125,
        'mahalla_id' => 5680,
        'type' => AddressTypeEnum::LIVING_PLACE->value,
        'address' => 'Yangibozor tumani Хорезмская область, Янгибазарский район, Чўболончи ССГ, Олтинкул МСГ, ул. Огайнилар, дом 21',
    ];

    private array $_phonesInput = [
        'phone' => '998933993300',
        'comment' => 'shaxsiy telefon raqami',
        'type' => PhoneTypeEnum::PRIVATE->value,
    ];

    protected function setUp(): void
    {
        parent::setUp();

        Customer::query()->forceDelete();
        Card::query()->forceDelete();
        Address::query()->forceDelete();
        Phone::query()->forceDelete();
    }

    #[Test]
    public function successCreateWithoutAddressesAndPhones()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery(customer: $this->_customerInput)
            );

        $this->assertSuccessCreateCustomerResponse($response);
    }

    #[Test]
    public function successCreateWithAddresses()
    {
        Artisan::call('tenants:seed', [
            '--class' => LocationSeeder::class,
            '--tenants' => [tenant()->id],
        ]);
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery(customer: $this->_customerInput, addresses: [$this->_addressInput])
            );

        $this->assertSuccessCreateCustomerResponse($response, addresses: true);
    }

    #[Test]
    public function successCreateWithPhones()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery(customer: $this->_customerInput, phones: [$this->_phonesInput])
            );

        $this->assertSuccessCreateCustomerResponse($response, phones: true);
    }

    #[Test]
    public function successCreateWithDuplicateTin()
    {
        $customer = Customer::factory()->create();
        $this->_customerInput['tin'] = $customer->tin;

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery(array_merge($this->_customerInput))
            );

        $this->assertSuccessCreateCustomerResponse($response);
        $duplicateTinCustomer = Customer::query()->findOrFail($response->json('data.createCustomer.id'));
        $this->assertNotEquals($customer->id, $duplicateTinCustomer->id);
        $this->assertEquals($customer->tin, $duplicateTinCustomer->tin);
    }

    #[Test]
    public function errorUnique()
    {
        // unique pinfl
        Customer::factory()->create(['pinfl' => $this->_customerInput['pinfl']]);
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery($this->_customerInput)
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.unique', ['attribute' => 'customer.pinfl']), $response->json('errors.0.extensions.validation')['customer.pinfl'][0]);

        // unique passport
        Customer::query()->forceDelete();
        Customer::factory()->create(['passport_series' => $this->_customerInput['passport_series'], 'passport_number' => $this->_customerInput['passport_number']]);
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery($this->_customerInput)
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.unique', ['attribute' => 'customer.passport number']), $response->json('errors.0.extensions.validation')['customer.passport_number'][0]);
    }

    #[Test]
    public function errorCustomerValidation()
    {
        // required errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery(array_merge($this->_customerInput, [
                    'pinfl' => null,
                    'passport_series' => null,
                    'passport_number' => null,
                    'birth_date' => null,
                    'birth_place' => null,
                    'first_name' => null,
                    'gender' => null,
                    'last_name' => null,
                    'passport_citizenship' => null,
                    'passport_expiration_date' => null,
                    'passport_given_date' => null,
                    'passport_given_place' => null,
                ]))
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$customer" got invalid value null at "customer.pinfl"; Expected non-nullable type "BigInteger!" not to be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$customer" got invalid value null at "customer.passport_series"; Expected non-nullable type "String!" not to be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$customer" got invalid value null at "customer.passport_number"; Expected non-nullable type "String!" not to be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$customer" got invalid value null at "customer.passport_given_date"; Expected non-nullable type "String!" not to be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$customer" got invalid value null at "customer.passport_expiration_date"; Expected non-nullable type "String!" not to be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$customer" got invalid value null at "customer.passport_given_place"; Expected non-nullable type "String!" not to be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$customer" got invalid value null at "customer.passport_citizenship"; Expected non-nullable type "String!" not to be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$customer" got invalid value null at "customer.first_name"; Expected non-nullable type "String!" not to be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$customer" got invalid value null at "customer.last_name"; Expected non-nullable type "String!" not to be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$customer" got invalid value null at "customer.birth_date"; Expected non-nullable type "String!" not to be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$customer" got invalid value null at "customer.birth_place"; Expected non-nullable type "String!" not to be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$customer" got invalid value null at "customer.gender"; Expected non-nullable type "Int!" not to be null.'));

        // validation errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery(array_merge($this->_customerInput, [
                    'pinfl' => '315039531300',
                    'tin' => '5202120',
                    'passport_series' => '22',
                    'passport_number' => '31193AA',
                    'birth_date' => 'aaa',
                    'birth_place' => Str::random(33),
                    'first_name' => Str::random(33),
                    'gender' => 5,
                    'last_name' => Str::random(33),
                    'middle_name' => Str::random(33),
                    'passport_citizenship' => Str::random(33),
                    'passport_expiration_date' => 'aaa',
                    'passport_given_date' => 'aaa',
                    'passport_given_place' => Str::random(129),
                ]))
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.regex', ['attribute' => 'customer.passport series']), $response->json('errors.0.extensions.validation')['customer.passport_series'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'customer.passport given place', 'max' => 128]), $response->json('errors.0.extensions.validation')['customer.passport_given_place'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'customer.passport citizenship', 'max' => 32]), $response->json('errors.0.extensions.validation')['customer.passport_citizenship'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'customer.first name', 'max' => 32]), $response->json('errors.0.extensions.validation')['customer.first_name'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'customer.last name', 'max' => 32]), $response->json('errors.0.extensions.validation')['customer.last_name'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'customer.middle name', 'max' => 32]), $response->json('errors.0.extensions.validation')['customer.middle_name'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'customer.birth place', 'max' => 32]), $response->json('errors.0.extensions.validation')['customer.birth_place'][0]);
        $this->assertEquals(trans('validation.in', ['attribute' => 'customer.gender']), $response->json('errors.0.extensions.validation')['customer.gender'][0]);
        $this->assertEquals(trans('validation.date', ['attribute' => 'customer.birth date']), $response->json('errors.0.extensions.validation')['customer.birth_date'][0]);
        $this->assertEquals(trans('validation.date', ['attribute' => 'customer.passport expiration date']), $response->json('errors.0.extensions.validation')['customer.passport_expiration_date'][0]);
        $this->assertEquals(trans('validation.date', ['attribute' => 'customer.passport given date']), $response->json('errors.0.extensions.validation')['customer.passport_given_date'][0]);
    }

    #[Test]
    public function errorAddressValidation()
    {
        // required errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery($this->_customerInput, addresses: [array_merge($this->_addressInput, [
                    'type' => null,
                    'address' => null,
                    'region_id' => null,
                    'district_id' => null,
                ])])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$addresses" got invalid value null at "addresses[0].type"; Expected non-nullable type "Int!" not to be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$addresses" got invalid value null at "addresses[0].address"; Expected non-nullable type "String!" not to be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$addresses" got invalid value null at "addresses[0].region_id"; Expected non-nullable type "Int!" not to be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$addresses" got invalid value null at "addresses[0].district_id"; Expected non-nullable type "Int!" not to be null.'));

        // validation errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery($this->_customerInput, addresses: [array_merge($this->_addressInput, [
                    'type' => 99,
                    'address' => Str::random(256),
                    'region_id' => 999999999,
                    'district_id' => 999999999,
                    'mahalla_id' => 999999999,
                ])])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.in', ['attribute' => 'addresses.0.type']), $response->json('errors.0.extensions.validation')['addresses.0.type'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'addresses.0.address', 'max' => 255]), $response->json('errors.0.extensions.validation')['addresses.0.address'][0]);
        $this->assertEquals(trans('validation.exists', ['attribute' => 'addresses.0.region id']), $response->json('errors.0.extensions.validation')['addresses.0.region_id'][0]);
        $this->assertEquals(trans('validation.exists', ['attribute' => 'addresses.0.district id']), $response->json('errors.0.extensions.validation')['addresses.0.district_id'][0]);
        $this->assertEquals(trans('validation.exists', ['attribute' => 'addresses.0.mahalla id']), $response->json('errors.0.extensions.validation')['addresses.0.mahalla_id'][0]);
    }

    #[Test]
    public function errorPhoneValidation()
    {
        // required errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery($this->_customerInput, phones: [array_merge($this->_phonesInput, [
                    'type' => null,
                    'phone' => null,
                ])])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$phones" got invalid value null at "phones[0].type"; Expected non-nullable type "Int!" not to be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$phones" got invalid value null at "phones[0].phone"; Expected non-nullable type "BigInteger!" not to be null.'));

        // validation errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery($this->_customerInput, phones: [array_merge($this->_phonesInput, [
                    'type' => 99,
                    'comment' => Str::random(129),
                    'phone' => '1',
                ])])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.in', ['attribute' => 'phones.0.type']), $response->json('errors.0.extensions.validation')['phones.0.type'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'phones.0.comment', 'max' => 128]), $response->json('errors.0.extensions.validation')['phones.0.comment'][0]);
        $this->assertEquals(trans('validation.digits_between', ['attribute' => 'phones.0.phone', 'min' => 9, 'max' => 13]), $response->json('errors.0.extensions.validation')['phones.0.phone'][0]);
    }

    private function buildGraphQLQuery(array $customer, array $addresses = [], array $phones = []): array
    {
        $query = [];
        $query['variables'] = ['customer' => $customer, 'addresses' => $addresses, 'phones' => $phones];
        $query['query'] = '
            mutation ($customer: CustomerInputGraphQLType!, $phones: [PhoneInputGraphQLType!]!, $addresses: [AddressInputGraphQLType!]!) {
                createCustomer(customer: $customer, phones: $phones, addresses: $addresses) {
                    id
                }
            }
        ';

        return $query;
    }

    private function assertSuccessCreateCustomerResponse(TestResponse $response, $addresses = false, $phones = false): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'createCustomer' => [
                    'id',
                ],
            ],
        ]);
        $customer = Customer::query()->find($response->json('data.createCustomer.id'));
        $this->assertNotNull($customer);
        $this->assertEquals($customer->pinfl, $this->_customerInput['pinfl']);
        $this->assertEquals($customer->tin, $this->_customerInput['tin']);
        $this->assertEquals($customer->passport_series, $this->_customerInput['passport_series']);
        $this->assertEquals($customer->passport_number, $this->_customerInput['passport_number']);
        $this->assertEquals($customer->birth_date->format('d-m-Y'), $this->_customerInput['birth_date']);
        $this->assertEquals($customer->birth_place, $this->_customerInput['birth_place']);
        $this->assertEquals($customer->first_name, $this->_customerInput['first_name']);
        $this->assertEquals($customer->last_name, $this->_customerInput['last_name']);
        $this->assertEquals($customer->middle_name, $this->_customerInput['middle_name']);
        $this->assertEquals($customer->gender->value, $this->_customerInput['gender']);
        $this->assertEquals($customer->passport_citizenship, $this->_customerInput['passport_citizenship']);
        $this->assertEquals($customer->passport_expiration_date->format('d-m-Y'), $this->_customerInput['passport_expiration_date']);
        $this->assertEquals($customer->passport_given_date->format('d-m-Y'), $this->_customerInput['passport_given_date']);
        $this->assertEquals($customer->passport_given_place, $this->_customerInput['passport_given_place']);

        if ($addresses) {
            $this->assertNotEmpty($customer->addresses);
            $this->assertEquals(1, $customer->addresses->count());
            $this->assertEquals($customer->addresses[0]->address, $this->_addressInput['address']);
            $this->assertEquals($customer->addresses[0]->region_id, $this->_addressInput['region_id']);
            $this->assertEquals($customer->addresses[0]->district_id, $this->_addressInput['district_id']);
            $this->assertEquals($customer->addresses[0]->mahalla_id, $this->_addressInput['mahalla_id']);
            $this->assertEquals($customer->addresses[0]->type->value, $this->_addressInput['type']);
        }

        if ($phones) {
            $this->assertNotEmpty($customer->phones);
            $this->assertEquals(1, $customer->phones->count());
            $this->assertEquals($customer->phones[0]->pivot->type, $this->_phonesInput['type']);
            $this->assertEquals($customer->phones[0]->pivot->comment, $this->_phonesInput['comment']);
            $this->assertEquals($customer->phones[0]->phone, $this->_phonesInput['phone']);
        }
    }
}
