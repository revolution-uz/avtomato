<?php

namespace Tests\GraphQL\Ecommerce\Client\Queries;

use App\Models\Company\Address;
use App\Models\Company\Ecommerce\User;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class AddressesGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private User $_user;

    private int $_totalAddressCount = 5;

    private array $_structure = [
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
            'name_uz',
            'name_oz',
            'name_ru',
            'name_en',
        ],
        'district' => [
            'id',
            'name',
            'name_uz',
            'name_oz',
            'name_ru',
            'name_en',
            'region_id',
        ],
        'mahalla' => [
            'id',
            'name',
            'name_uz',
            'name_oz',
            'name_ru',
            'name_en',
            'district_id',
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->_user = $this->createEcommerceClientUser();

        Address::query()->forceDelete();
        Address::factory()
            ->count($this->_totalAddressCount)
            ->create(['addressable_type' => User::class, 'addressable_id' => $this->_user->id]);
    }

    #[Test]
    public function successAddress(): void
    {
        $response = $this
            ->actingAs($this->_user, 'ecommerce-client')
            ->postJson(route('graphql.ecommerce-client'), $this->buildGraphQLQuery());

        $this->assertSuccessAddressResponse($response);
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        return [
            'query' => <<<GRAPHQL
                query Addresses {
                    addresses {
                        {$this->convertIntoQuery($this->_structure)}
                    }
                }
            GRAPHQL,
            'variables' => $variables,
        ];
    }

    private function assertSuccessAddressResponse(TestResponse $response): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure(['data' => ['addresses' => ['*' => $this->_structure]]]);

        $this->assertCount($this->_totalAddressCount, $response->json('data.addresses'));

        /** @var Address $address */
        $address = $this->_user->addresses()->orderBy('created_at', 'desc')->first();

        $this->assertEquals($address->id, $response->json('data.addresses.0.id'));
        $this->assertEquals($address->address, $response->json('data.addresses.0.address'));
        $this->assertEquals($address->type->value, $response->json('data.addresses.0.type'));
        $this->assertEquals($address->region_id, $response->json('data.addresses.0.region_id'));
        $this->assertEquals($address->district_id, $response->json('data.addresses.0.district_id'));
        $this->assertEquals($address->mahalla_id, $response->json('data.addresses.0.mahalla_id'));
        $this->assertEquals($address->created_at->timestamp, $response->json('data.addresses.0.created_at'));
        $this->assertEquals($address->updated_at->timestamp, $response->json('data.addresses.0.updated_at'));
        $this->assertEquals($address->deleted_at?->timestamp, $response->json('data.addresses.0.deleted_at'));

        // Region
        $this->assertEquals($address->region->id, $response->json('data.addresses.0.region.id'));
        $this->assertEquals($address->region->name, $response->json('data.addresses.0.region.name'));
        $this->assertEquals($address->region->name_uz, $response->json('data.addresses.0.region.name_uz'));
        $this->assertEquals($address->region->name_oz, $response->json('data.addresses.0.region.name_oz'));
        $this->assertEquals($address->region->name_ru, $response->json('data.addresses.0.region.name_ru'));
        $this->assertEquals($address->region->name_en, $response->json('data.addresses.0.region.name_en'));

        // District
        $this->assertEquals($address->district->id, $response->json('data.addresses.0.district.id'));
        $this->assertEquals($address->district->name, $response->json('data.addresses.0.district.name'));
        $this->assertEquals($address->district->name_uz, $response->json('data.addresses.0.district.name_uz'));
        $this->assertEquals($address->district->name_oz, $response->json('data.addresses.0.district.name_oz'));
        $this->assertEquals($address->district->name_ru, $response->json('data.addresses.0.district.name_ru'));
        $this->assertEquals($address->district->name_en, $response->json('data.addresses.0.district.name_en'));
        $this->assertEquals($address->district->region_id, $response->json('data.addresses.0.district.region_id'));

        // Mahalla
        $this->assertEquals($address->mahalla->id, $response->json('data.addresses.0.mahalla.id'));
        $this->assertEquals($address->mahalla->name, $response->json('data.addresses.0.mahalla.name'));
        $this->assertEquals($address->mahalla->name_uz, $response->json('data.addresses.0.mahalla.name_uz'));
        $this->assertEquals($address->mahalla->name_oz, $response->json('data.addresses.0.mahalla.name_oz'));
        $this->assertEquals($address->mahalla->name_ru, $response->json('data.addresses.0.mahalla.name_ru'));
        $this->assertEquals($address->mahalla->name_en, $response->json('data.addresses.0.mahalla.name_en'));
        $this->assertEquals($address->mahalla->district_id, $response->json('data.addresses.0.mahalla.district_id'));
    }
}
