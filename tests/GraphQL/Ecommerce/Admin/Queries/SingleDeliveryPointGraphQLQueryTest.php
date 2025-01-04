<?php

namespace Tests\GraphQL\Ecommerce\Admin\Queries;

use App\Models\Company\Ecommerce\DeliveryPoint;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class SingleDeliveryPointGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private array $_structure = [
        'id',
        'name_uz',
        'name_ru',
        'name_en',
        'name_oz',
        'description_uz',
        'description_ru',
        'description_en',
        'description_oz',
        'is_prepaid',
        'address_id',
        'created_at',
        'updated_at',
        'deleted_at',
        'address' => [
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
            'mahalla' => [
                'id',
                'district_id',
                'name',
            ],
        ],
    ];

    #[Test]
    public function successSingleDeliveryPoint()
    {
        /** @var DeliveryPoint $deliveryPoint */
        $deliveryPoint = DeliveryPoint::factory()->create();

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['id' => $deliveryPoint->id]));

        $this->assertSuccessSingleDeliveryPointResponse($response, $deliveryPoint);
    }

    #[Test]
    public function errorValidation()
    {
        // GraphQL type error
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['id' => null]));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('Variable "$id" of non-null type "Int!" must not be null.', $response->json('errors.0.message'));

        // Rule errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['id' => 999999999]));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'id']), $response->json('errors.0.extensions.validation.id.0'));
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        return [
            'query' => <<<GRAPHQL
                query SingleDeliveryPoint(\$id: Int!) {
                    singleDeliveryPoint(id: \$id) {
                        {$this->convertIntoQuery($this->_structure)}
                    }
                }
            GRAPHQL,
            'variables' => $variables,
        ];
    }

    private function assertSuccessSingleDeliveryPointResponse(TestResponse $response, DeliveryPoint $deliveryPoint): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'singleDeliveryPoint' => $this->_structure,
            ],
        ]);

        $this->assertEquals($deliveryPoint->id, $response->json('data.singleDeliveryPoint.id'));
        $this->assertEquals($deliveryPoint->name_uz, $response->json('data.singleDeliveryPoint.name_uz'));
        $this->assertEquals($deliveryPoint->name_ru, $response->json('data.singleDeliveryPoint.name_ru'));
        $this->assertEquals($deliveryPoint->name_en, $response->json('data.singleDeliveryPoint.name_en'));
        $this->assertEquals($deliveryPoint->name_oz, $response->json('data.singleDeliveryPoint.name_oz'));
        $this->assertEquals($deliveryPoint->description_uz, $response->json('data.singleDeliveryPoint.description_uz'));
        $this->assertEquals($deliveryPoint->description_ru, $response->json('data.singleDeliveryPoint.description_ru'));
        $this->assertEquals($deliveryPoint->description_en, $response->json('data.singleDeliveryPoint.description_en'));
        $this->assertEquals($deliveryPoint->description_oz, $response->json('data.singleDeliveryPoint.description_oz'));
        $this->assertEquals($deliveryPoint->is_prepaid, $response->json('data.singleDeliveryPoint.is_prepaid'));
        $this->assertEquals($deliveryPoint->address_id, $response->json('data.singleDeliveryPoint.address_id'));
        $this->assertEquals($deliveryPoint->created_at->timestamp, $response->json('data.singleDeliveryPoint.created_at'));
        $this->assertEquals($deliveryPoint->updated_at->timestamp, $response->json('data.singleDeliveryPoint.updated_at'));
        $this->assertEquals($deliveryPoint->deleted_at?->timestamp, $response->json('data.singleDeliveryPoint.deleted_at'));

        // address
        $this->assertEquals($deliveryPoint->address->id, $response->json('data.singleDeliveryPoint.address.id'));
        $this->assertEquals($deliveryPoint->address->address, $response->json('data.singleDeliveryPoint.address.address'));
        $this->assertEquals($deliveryPoint->address->type->value, $response->json('data.singleDeliveryPoint.address.type'));
        $this->assertEquals($deliveryPoint->address->region_id, $response->json('data.singleDeliveryPoint.address.region_id'));
        $this->assertEquals($deliveryPoint->address->district_id, $response->json('data.singleDeliveryPoint.address.district_id'));
        $this->assertEquals($deliveryPoint->address->mahalla_id, $response->json('data.singleDeliveryPoint.address.mahalla_id'));
        $this->assertEquals($deliveryPoint->address->created_at->timestamp, $response->json('data.singleDeliveryPoint.address.created_at'));
        $this->assertEquals($deliveryPoint->address->updated_at->timestamp, $response->json('data.singleDeliveryPoint.address.updated_at'));
        $this->assertEquals($deliveryPoint->address->deleted_at?->timestamp, $response->json('data.singleDeliveryPoint.address.deleted_at'));

        // address -> region
        $this->assertEquals($deliveryPoint->address->region->id, $response->json('data.singleDeliveryPoint.address.region.id'));
        $this->assertEquals($deliveryPoint->address->region->name, $response->json('data.singleDeliveryPoint.address.region.name'));

        // address -> district
        $this->assertEquals($deliveryPoint->address->district->id, $response->json('data.singleDeliveryPoint.address.district.id'));
        $this->assertEquals($deliveryPoint->address->district->region_id, $response->json('data.singleDeliveryPoint.address.district.region_id'));
        $this->assertEquals($deliveryPoint->address->district->name, $response->json('data.singleDeliveryPoint.address.district.name'));

        // address -> mahalla
        $this->assertEquals($deliveryPoint->address->mahalla->id, $response->json('data.singleDeliveryPoint.address.mahalla.id'));
        $this->assertEquals($deliveryPoint->address->mahalla->district_id, $response->json('data.singleDeliveryPoint.address.mahalla.district_id'));
        $this->assertEquals($deliveryPoint->address->mahalla->name, $response->json('data.singleDeliveryPoint.address.mahalla.name'));
    }
}
