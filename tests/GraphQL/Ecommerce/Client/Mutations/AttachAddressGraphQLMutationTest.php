<?php

namespace Tests\GraphQL\Ecommerce\Client\Mutations;

use App\Models\Company\Address;
use App\Models\Company\District;
use App\Models\Company\Ecommerce\User;
use App\Models\Company\Mahalla;
use App\Models\Company\Region;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class AttachAddressGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private User $_user;

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
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->_user = $this->createEcommerceClientUser();
        Address::query()->forceDelete();
    }

    #[Test]
    public function successAttachAddressWithOnlyRequiredAttributes(): void
    {
        $variables = $this->getInputVariables(true);

        $response = $this
            ->actingAs($this->_user, 'ecommerce-client')
            ->postJson(route('graphql.ecommerce-client'), $this->buildGraphQLQuery($variables));

        $this->assertSuccessAttachAddressResponse($response, $variables);
    }

    #[Test]
    public function successAttachAddress(): void
    {
        $variables = $this->getInputVariables();

        $response = $this
            ->actingAs($this->_user, 'ecommerce-client')
            ->postJson(route('graphql.ecommerce-client'), $this->buildGraphQLQuery($variables));

        $this->assertSuccessAttachAddressResponse($response, $variables);
    }

    #[Test]
    public function errorValidation(): void
    {
        // GraphQL type errors
        $response = $this
            ->actingAs($this->_user, 'ecommerce-client')
            ->postJson(route('graphql.ecommerce-client'), $this->buildGraphQLQuery());
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$address" of required type "String!" was not provided.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$region_id" of required type "Int!" was not provided.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$district_id" of required type "Int!" was not provided.'));

        // Rule errors
        $variables = $this->getInputVariables(variables: [
            'address' => Str::random(129),
            'region_id' => 999999999,
            'district_id' => 999999999,
            'mahalla_id' => 999999999,
        ]);
        $response = $this
            ->actingAs($this->_user, 'ecommerce-client')
            ->postJson(route('graphql.ecommerce-client'), $this->buildGraphQLQuery($variables));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'address', 'max' => 128]), $response->json('errors.0.extensions.validation.address.0'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'region id']), $response->json('errors.0.extensions.validation.region_id.0'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'district id']), $response->json('errors.0.extensions.validation.district_id.0'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'mahalla id']), $response->json('errors.0.extensions.validation.mahalla_id.0'));
    }

    protected function buildGraphQLQuery(array $variables = []): array
    {
        return [
            'query' => <<<GRAPHQL
                mutation AttachAddress(
                    \$address: String!
                    \$region_id: Int!
                    \$district_id: Int!
                    \$mahalla_id: Int
                ) {
                    attachAddress(
                        address: \$address
                        region_id: \$region_id
                        district_id: \$district_id
                        mahalla_id: \$mahalla_id
                    ) {
                        {$this->convertIntoQuery($this->_structure)}
                    }
                }
            GRAPHQL,
            'variables' => $variables,
        ];
    }

    private function assertSuccessAttachAddressResponse(TestResponse $response, array $variables, bool $isRequired = false): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure(['data' => ['attachAddress' => $this->_structure]]);

        /** @var Address $address */
        $address = $this->_user->addresses()->first();
        $this->assertNotNull($address);
        $this->assertEquals($variables['address'], $address->address);
        $this->assertEquals($variables['region_id'], $address->region_id);
        $this->assertEquals($variables['district_id'], $address->district_id);
        ! $isRequired ?
            $this->assertEquals($variables['mahalla_id'], $address->mahalla_id) :
            $this->assertNull($address->mahalla_id);

        $this->assertEquals($address->id, $response->json('data.attachAddress.id'));
        $this->assertEquals($address->address, $response->json('data.attachAddress.address'));
        $this->assertEquals($address->type->value, $response->json('data.attachAddress.type'));
        $this->assertEquals($address->region_id, $response->json('data.attachAddress.region_id'));
        $this->assertEquals($address->district_id, $response->json('data.attachAddress.district_id'));
        ! $isRequired ?
            $this->assertEquals($address->mahalla_id, $response->json('data.attachAddress.mahalla_id')) :
            $this->assertNull($response->json('data.attachAddress.mahalla_id'));
        $this->assertEquals($address->created_at->timestamp, $response->json('data.attachAddress.created_at'));
        $this->assertEquals($address->updated_at->timestamp, $response->json('data.attachAddress.updated_at'));
        $this->assertEquals($address->deleted_at?->timestamp, $response->json('data.attachAddress.deleted_at'));
    }

    private function getInputVariables(bool $isRequired = false, array $variables = [])
    {
        return array_merge(
            [
                'address' => Str::random(128),
                'region_id' => Region::factory()->create()->id,
                'district_id' => District::factory()->create()->id,
                'mahalla_id' => ! $isRequired ? Mahalla::factory()->create()->id : null,
            ],
            $variables
        );
    }
}
