<?php

namespace Customer\Mutations;

use App\Enums\Company\AddressTypeEnum;
use App\Models\Company\Address;
use App\Models\Company\Customer;
use App\Models\Company\District;
use App\Models\Company\Mahalla;
use App\Models\Company\Region;
use Database\Seeders\Company\LocationSeeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class AttachAddressGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private Customer $_customer;

    private array $_input;

    protected function setUp(): void
    {
        parent::setUp();

        Artisan::call('tenants:seed', [
            '--class' => LocationSeeder::class,
            '--tenants' => [tenant()->id],
        ]);

        $this->_customer = Customer::factory()->create();

        $this->_input = [
            'customer_id' => $this->_customer->id,
            'type' => AddressTypeEnum::LIVING_PLACE->value,
            'address' => 'Yangibozor tumani Хорезмская область, Янгибазарский район, Чўболончи ССГ, Олтинкул МСГ, ул. Огайнилар, дом 21',
            'region_id' => 13,
            'district_id' => 125,
            'mahalla_id' => null,
        ];
    }

    #[Test]
    public function successAttachWithoutMahalla()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery($this->_input)
            );

        $this->assertSuccessAttachAddressResponse($response);
    }

    #[Test]
    public function successCreateWithMahalla()
    {
        $this->_input['mahalla_id'] = 5680;

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery($this->_input)
            );

        $this->assertSuccessAttachAddressResponse($response);

        $response->assertJsonStructure([
            'data' => [
                'attachAddress' => [
                    'mahalla' => [
                        'id',
                        'district_id',
                        'name',
                    ],
                ],
            ],
        ]);
        $address = Address::query()->find($response->json('data.attachAddress.id'));
        $this->assertEquals($this->_input['mahalla_id'], $address->mahalla_id);
        $this->assertEquals($address->mahalla->id, $response->json('data.attachAddress.mahalla.id'));
        $this->assertEquals($address->mahalla->district_id, $response->json('data.attachAddress.mahalla.district_id'));
        $this->assertEquals($address->mahalla->name, $response->json('data.attachAddress.mahalla.name'));

    }

    #[Test]
    public function errorCustomerValidation()
    {
        // required errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery([
                    'customer_id' => null,
                    'type' => null,
                    'address' => null,
                    'region_id' => null,
                    'district_id' => null,
                    'mahalla_id' => null,
                ])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$customer_id" of non-null type "Int!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$type" of non-null type "Int!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$address" of non-null type "String!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$region_id" of non-null type "Int!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$district_id" of non-null type "Int!" must not be null.'));

        // validation errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery([
                    'customer_id' => Customer::query()->orderByDesc('id')->first()->id + 1,
                    'type' => 1000,
                    'address' => Str::random(129),
                    'region_id' => Region::query()->orderByDesc('id')->first()->id + 1,
                    'district_id' => District::query()->orderByDesc('id')->first()->id + 1,
                    'mahalla_id' => Mahalla::query()->orderByDesc('id')->first()->id + 1,
                ])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'customer id']), $response->json('errors.0.extensions.validation.customer_id.0'));
        $this->assertEquals(trans('validation.in', ['attribute' => 'type']), $response->json('errors.0.extensions.validation.type.0'));
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'address', 'max' => 128]), $response->json('errors.0.extensions.validation.address.0'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'region id']), $response->json('errors.0.extensions.validation.region_id.0'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'district id']), $response->json('errors.0.extensions.validation.district_id.0'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'mahalla id']), $response->json('errors.0.extensions.validation.mahalla_id.0'));
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $query['query'] = '
            mutation AttachAddress(
                $customer_id: Int!
                $type: Int!
                $address: String!
                $region_id: Int!
                $district_id: Int!
                $mahalla_id: Int
            ) {
                attachAddress(
                    customer_id: $customer_id
                    type: $type
                    address: $address
                    region_id: $region_id
                    district_id: $district_id
                    mahalla_id: $mahalla_id
                ) {
                    id
                    address
                    type
                    region_id
                    district_id
                    mahalla_id
                    created_at
                    updated_at
                    deleted_at
                    region {
                        id
                        name
                    }
                    district {
                        id
                        region_id
                        name
                    }
                    mahalla {
                        id
                        district_id
                        name
                    }
                }
            }
        ';

        return $query;
    }

    private function assertSuccessAttachAddressResponse(TestResponse $response): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'attachAddress' => [
                    'id',
                    'address',
                    'type',
                    'region_id',
                    'district_id',
                    'mahalla_id',
                    'created_at',
                    'updated_at',
                    'deleted_at',
                    'region' => [
                        'id',
                        'name',
                    ],
                    'district' => [
                        'id',
                        'region_id',
                        'name',
                    ],
                ],
            ],
        ]);
        $address = Address::query()->find($response->json('data.attachAddress.id'));
        $this->assertNotNull($address);
        $this->assertEquals($this->_input['type'], $address->type->value);
        $this->assertEquals($this->_input['address'], $address->address);
        $this->assertEquals($this->_input['region_id'], $address->region_id);
        $this->assertEquals($this->_input['district_id'], $address->district_id);

        $this->assertEquals($address->address, $response->json('data.attachAddress.address'));
        $this->assertEquals($address->type->value, $response->json('data.attachAddress.type'));
        $this->assertEquals($address->region_id, $response->json('data.attachAddress.region_id'));
        $this->assertEquals($address->district_id, $response->json('data.attachAddress.district_id'));
        $this->assertEquals($address->mahalla_id, $response->json('data.attachAddress.mahalla_id'));
        $this->assertEquals($address->created_at->timestamp, $response->json('data.attachAddress.created_at'));
        $this->assertEquals($address->updated_at->timestamp, $response->json('data.attachAddress.updated_at'));
        $this->assertEquals($address->deleted_at?->timestamp, $response->json('data.attachAddress.deleted_at'));
        $this->assertEquals($address->region->id, $response->json('data.attachAddress.region.id'));
        $this->assertEquals($address->region->name, $response->json('data.attachAddress.region.name'));
        $this->assertEquals($address->district->id, $response->json('data.attachAddress.district.id'));
        $this->assertEquals($address->district->region_id, $response->json('data.attachAddress.district.region_id'));
        $this->assertEquals($address->district->name, $response->json('data.attachAddress.district.name'));

        $address = $this->_customer->addresses()->first();
        $this->assertNotNull($address);
        $this->assertEquals($address->id, $response->json('data.attachAddress.id'));
    }
}
