<?php

namespace Tests\GraphQL\Customer\Queries;

use App\Enums\Company\PhoneTypeEnum;
use App\Models\Company\Address;
use App\Models\Company\Card;
use App\Models\Company\Customer;
use App\Models\Company\Phone;
use Illuminate\Testing\TestResponse;
use Tests\GraphQL\GraphQLTestCase;

class SingleCustomerGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private Customer $_customer;

    protected function setUp(): void
    {
        parent::setUp();

        Customer::query()->forceDelete();
        $this->_customer = Customer::factory()->create();
        Card::query()->forceDelete();
        Card::factory()->create(['customer_id' => $this->_customer->id]);
        Address::query()->forceDelete();
        Address::factory()->count(1)->create(['addressable_type' => Customer::class, 'addressable_id' => $this->_customer->id]);
        Phone::query()->forceDelete();
        foreach (Phone::factory()->count(1)->create() as $phone) {
            $this->_customer->phones()->attach($phone->id, ['type' => PhoneTypeEnum::randomValue()]);
        }
    }

    /**
     * @test
     */
    public function successSingleCustomer()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery($this->_customer->id)
            );

        $this->assertSuccessSingleCustomerResponse($response);
    }

    /**
     * @test
     */
    public function errorSingleCustomer()
    {
        // not found
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery(99999999)
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'id']), $response->json('errors.0.extensions.validation.id.0'));

        // deleted
        $this->_customer->delete();
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.customer'),
                $this->buildGraphQLQuery($this->_customer->id)
            );

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
                singleCustomer(id: $id) {
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
                    cards {
                        id
                        customer_id
                        number
                        card_type
                        owner
                        expiry
                        is_connected
                        phone
                        status
                        balance
                        created_at
                        updated_at
                    }
                    addresses {
                        id
                        address
                        type
                        region_id
                        region {
                            id
                            name
                        }
                        district_id
                        district {
                            id
                            region_id
                            name
                        }
                        mahalla_id
                        mahalla {
                            id
                            district_id
                            name
                        }
                        created_at
                        updated_at
                        deleted_at
                    }
                    phones {
                        id
                        phone
                        type
                        comment
                        created_at
                        updated_at
                    }
                }
            }
        ';

        return $query;
    }

    private function assertSuccessSingleCustomerResponse(TestResponse $response): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'singleCustomer' => [
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
                    'cards' => [
                        '*' => [
                            'customer_id',
                            'number',
                            'card_type',
                            'owner',
                            'expiry',
                            'is_connected',
                            'phone',
                            'status',
                            'balance',
                            'created_at',
                            'updated_at',
                        ],
                    ],
                    'addresses' => [
                        '*' => [
                            'address',
                            'type',
                            'region_id',
                            'region' => [
                                'id',
                                'name',
                            ],
                            'district_id',
                            'district' => [
                                'id',
                                'region_id',
                                'name',
                            ],
                            'mahalla_id',
                            'mahalla' => [
                                'id',
                                'district_id',
                                'name',
                            ],
                            'created_at',
                            'updated_at',
                            'deleted_at',
                        ],
                    ],
                    'phones' => [
                        '*' => [
                            'id',
                            'phone',
                            'type',
                            'comment',
                            'created_at',
                            'updated_at',
                        ],
                    ],
                ],
            ],
        ]);
        $customer = Customer::query()->find($response->json('data.singleCustomer.id'));
        $this->assertNotNull($customer);
        $this->assertEquals($customer->id, $response->json('data.singleCustomer.id'));
        $this->assertEquals($customer->pinfl, $response->json('data.singleCustomer.pinfl'));
        $this->assertEquals($customer->tin, $response->json('data.singleCustomer.tin'));
        $this->assertEquals($customer->passport_series, $response->json('data.singleCustomer.passport_series'));
        $this->assertEquals($customer->passport_number, $response->json('data.singleCustomer.passport_number'));
        $this->assertEquals($customer->passport_given_date?->format('d-m-Y'), $response->json('data.singleCustomer.passport_given_date'));
        $this->assertEquals($customer->passport_expiration_date?->format('d-m-Y'), $response->json('data.singleCustomer.passport_expiration_date'));
        $this->assertEquals($customer->passport_given_place, $response->json('data.singleCustomer.passport_given_place'));
        $this->assertEquals($customer->passport_citizenship, $response->json('data.singleCustomer.passport_citizenship'));
        $this->assertEquals($customer->first_name, $response->json('data.singleCustomer.first_name'));
        $this->assertEquals($customer->last_name, $response->json('data.singleCustomer.last_name'));
        $this->assertEquals($customer->middle_name, $response->json('data.singleCustomer.middle_name'));
        $this->assertEquals($customer->birth_date?->format('d-m-Y'), $response->json('data.singleCustomer.birth_date'));
        $this->assertEquals($customer->birth_place, $response->json('data.singleCustomer.birth_place'));
        $this->assertEquals($customer->gender->value, $response->json('data.singleCustomer.gender'));
        $this->assertEquals($customer->created_at->timestamp, $response->json('data.singleCustomer.created_at'));
        $this->assertEquals($customer->updated_at->timestamp, $response->json('data.singleCustomer.updated_at'));
        $this->assertEquals($customer->deleted_at, $response->json('data.singleCustomer.deleted_at'));

        // cards
        $this->assertEquals($customer->cards[0]->id, $response->json('data.singleCustomer.cards.0.id'));
        $this->assertEquals($customer->cards[0]->customer_id, $response->json('data.singleCustomer.cards.0.customer_id'));
        $this->assertEquals($customer->cards[0]->pan, $response->json('data.singleCustomer.cards.0.number'));
        $this->assertEquals($customer->cards[0]->card_type, $response->json('data.singleCustomer.cards.0.card_type'));
        $this->assertEquals($customer->cards[0]->owner, $response->json('data.singleCustomer.cards.0.owner'));
        $this->assertEquals($customer->cards[0]->expiry, $response->json('data.singleCustomer.cards.0.expiry'));
        $this->assertEquals($customer->cards[0]->is_connected, $response->json('data.singleCustomer.cards.0.is_connected'));
        $this->assertEquals($customer->cards[0]->phone, $response->json('data.singleCustomer.cards.0.phone'));
        $this->assertEquals($customer->cards[0]->status->value, $response->json('data.singleCustomer.cards.0.status'));
        $this->assertEquals($customer->cards[0]->balance, $response->json('data.singleCustomer.cards.0.balance'));
        $this->assertEquals($customer->cards[0]->created_at->timestamp, $response->json('data.singleCustomer.cards.0.created_at'));
        $this->assertEquals($customer->cards[0]->updated_at->timestamp, $response->json('data.singleCustomer.cards.0.updated_at'));

        // addresses
        $this->assertEquals($customer->addresses[0]->address, $response->json('data.singleCustomer.addresses.0.address'));
        $this->assertEquals($customer->addresses[0]->type->value, $response->json('data.singleCustomer.addresses.0.type'));
        $this->assertEquals($customer->addresses[0]->region_id, $response->json('data.singleCustomer.addresses.0.region_id'));
        $this->assertEquals($customer->addresses[0]->region->id, $response->json('data.singleCustomer.addresses.0.region.id'));
        $this->assertEquals($customer->addresses[0]->region->name, $response->json('data.singleCustomer.addresses.0.region.name'));
        $this->assertEquals($customer->addresses[0]->district_id, $response->json('data.singleCustomer.addresses.0.district_id'));
        $this->assertEquals($customer->addresses[0]->district->id, $response->json('data.singleCustomer.addresses.0.district.id'));
        $this->assertEquals($customer->addresses[0]->district->region_id, $response->json('data.singleCustomer.addresses.0.district.region_id'));
        $this->assertEquals($customer->addresses[0]->district->name, $response->json('data.singleCustomer.addresses.0.district.name'));
        $this->assertEquals($customer->addresses[0]->mahalla_id, $response->json('data.singleCustomer.addresses.0.mahalla_id'));
        $this->assertEquals($customer->addresses[0]->mahalla->district_id, $response->json('data.singleCustomer.addresses.0.mahalla.district_id'));
        $this->assertEquals($customer->addresses[0]->mahalla->name, $response->json('data.singleCustomer.addresses.0.mahalla.name'));
        $this->assertEquals($customer->addresses[0]->created_at->timestamp, $response->json('data.singleCustomer.addresses.0.updated_at'));
        $this->assertEquals($customer->addresses[0]->updated_at->timestamp, $response->json('data.singleCustomer.addresses.0.updated_at'));
        $this->assertEquals($customer->addresses[0]->deleted_at, $response->json('data.singleCustomer.addresses.0.deleted_at'));

        // phones
        $this->assertEquals($customer->phones[0]->id, $response->json('data.singleCustomer.phones.0.id'));
        $this->assertEquals($customer->phones[0]->phone, $response->json('data.singleCustomer.phones.0.phone'));
        $this->assertEquals($customer->phones[0]->pivot->type, $response->json('data.singleCustomer.phones.0.type'));
        $this->assertEquals($customer->phones[0]->pivot->comment, $response->json('data.singleCustomer.phones.0.comment'));
        $this->assertEquals($customer->phones[0]->pivot->created_at->timestamp, $response->json('data.singleCustomer.phones.0.created_at'));
        $this->assertEquals($customer->phones[0]->pivot->updated_at->timestamp, $response->json('data.singleCustomer.phones.0.updated_at'));
    }
}
