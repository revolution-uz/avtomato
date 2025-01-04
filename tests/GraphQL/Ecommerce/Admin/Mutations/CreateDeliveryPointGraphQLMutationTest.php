<?php

namespace Tests\GraphQL\Ecommerce\Admin\Mutations;

use App\Enums\Company\CompanyStatusEnum;
use App\Models\Company\Address;
use App\Models\Company\Ecommerce\DeliveryPoint;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class CreateDeliveryPointGraphQLMutationTest extends GraphQLTestCase
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
        ],
    ];

    #[Test]
    public function successCreateDeliveryPoint()
    {
        $variables = $this->getInputVariables();

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery($variables));

        $this->assertSuccessCreateDeliveryPointResponse($response, $variables);
    }

    #[Test]
    public function errorValidation(): void
    {
        // GraphQL type errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery());
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$name_uz" of required type "String!" was not provided.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$name_ru" of required type "String!" was not provided.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$name_en" of required type "String!" was not provided.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$name_oz" of required type "String!" was not provided.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$description_uz" of required type "String!" was not provided.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$description_ru" of required type "String!" was not provided.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$description_en" of required type "String!" was not provided.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$description_oz" of required type "String!" was not provided.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$is_prepaid" of required type "Boolean!" was not provided.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$address_id" of required type "Int!" was not provided.'));

        // Rule errors
        $variables = $this->getInputVariables([
            'name_uz' => Str::random(256),
            'name_ru' => Str::random(256),
            'name_en' => Str::random(256),
            'name_oz' => Str::random(256),
            'description_uz' => Str::random(256),
            'description_ru' => Str::random(256),
            'description_en' => Str::random(256),
            'description_oz' => Str::random(256),
            'address_id' => 999999999,
        ]);
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery($variables));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'delivery point.name uz', 'max' => 255]), $response->json('errors.0.extensions.validation')['deliveryPoint.name_uz'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'delivery point.name ru', 'max' => 255]), $response->json('errors.0.extensions.validation')['deliveryPoint.name_ru'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'delivery point.name en', 'max' => 255]), $response->json('errors.0.extensions.validation')['deliveryPoint.name_en'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'delivery point.name oz', 'max' => 255]), $response->json('errors.0.extensions.validation')['deliveryPoint.name_oz'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'delivery point.description uz', 'max' => 255]), $response->json('errors.0.extensions.validation')['deliveryPoint.description_uz'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'delivery point.description ru', 'max' => 255]), $response->json('errors.0.extensions.validation')['deliveryPoint.description_ru'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'delivery point.description en', 'max' => 255]), $response->json('errors.0.extensions.validation')['deliveryPoint.description_en'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'delivery point.description oz', 'max' => 255]), $response->json('errors.0.extensions.validation')['deliveryPoint.description_oz'][0]);
        $this->assertEquals(trans('validation.exists', ['attribute' => 'delivery point.address id']), $response->json('errors.0.extensions.validation')['deliveryPoint.address_id'][0]);
    }

    #[Test]
    public function errorCreateDeliveryPointWithInactiveCompany(): void
    {
        tenant()->updateOrFail(['status' => CompanyStatusEnum::INACTIVE]);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery($this->getInputVariables()));

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals(trans('api.inactive_company'), $response->json('errors.0.message'));
    }

    protected function buildGraphQLQuery(array $variables = []): array
    {
        return [
            'query' => <<<GRAPHQL
                mutation CreateDeliveryPoint(
                    \$name_uz: String!
                    \$name_ru: String!
                    \$name_en: String!
                    \$name_oz: String!
                    \$description_uz: String!
                    \$description_ru: String!
                    \$description_en: String!
                    \$description_oz: String!
                    \$is_prepaid: Boolean!
                    \$address_id: Int!
                ) {
                    createDeliveryPoint(
                        deliveryPoint: {
                            name_uz: \$name_uz
                            name_ru: \$name_ru
                            name_en: \$name_en
                            name_oz: \$name_oz
                            description_uz: \$description_uz
                            description_ru: \$description_ru
                            description_en: \$description_en
                            description_oz: \$description_oz
                            is_prepaid: \$is_prepaid
                            address_id: \$address_id
                        }
                    ) {
                        {$this->convertIntoQuery($this->_structure)}
                    }
                }
            GRAPHQL,
            'variables' => $variables,
        ];
    }

    private function getInputVariables(array $variables = [])
    {
        return array_merge(
            [
                'name_uz' => Str::random(255),
                'name_ru' => Str::random(255),
                'name_en' => Str::random(255),
                'name_oz' => Str::random(255),
                'description_uz' => Str::random(255),
                'description_ru' => Str::random(255),
                'description_en' => Str::random(255),
                'description_oz' => Str::random(255),
                'is_prepaid' => (bool) random_int(0, 1),
                'address_id' => Address::factory()->create()->id,
            ],
            $variables
        );
    }

    private function assertSuccessCreateDeliveryPointResponse(TestResponse $response, array $variables): void
    {
        $response->assertOk();
        $response->assertJsonStructure(['data' => ['createDeliveryPoint' => $this->_structure]]);

        $deliveryPoint = DeliveryPoint::query()->findOrFail($response->json('data.createDeliveryPoint.id'));

        $this->assertEquals($deliveryPoint->name_uz, $variables['name_uz']);
        $this->assertEquals($deliveryPoint->name_ru, $variables['name_ru']);
        $this->assertEquals($deliveryPoint->name_en, $variables['name_en']);
        $this->assertEquals($deliveryPoint->name_oz, $variables['name_oz']);
        $this->assertEquals($deliveryPoint->description_uz, $variables['description_uz']);
        $this->assertEquals($deliveryPoint->description_ru, $variables['description_ru']);
        $this->assertEquals($deliveryPoint->description_en, $variables['description_en']);
        $this->assertEquals($deliveryPoint->description_oz, $variables['description_oz']);
        $this->assertEquals($deliveryPoint->is_prepaid, $variables['is_prepaid']);
        $this->assertEquals($deliveryPoint->address_id, $variables['address_id']);

        $this->assertEquals($deliveryPoint->id, $response->json('data.createDeliveryPoint.id'));
        $this->assertEquals($deliveryPoint->name_uz, $response->json('data.createDeliveryPoint.name_uz'));
        $this->assertEquals($deliveryPoint->name_ru, $response->json('data.createDeliveryPoint.name_ru'));
        $this->assertEquals($deliveryPoint->name_en, $response->json('data.createDeliveryPoint.name_en'));
        $this->assertEquals($deliveryPoint->name_oz, $response->json('data.createDeliveryPoint.name_oz'));
        $this->assertEquals($deliveryPoint->description_uz, $response->json('data.createDeliveryPoint.description_uz'));
        $this->assertEquals($deliveryPoint->description_ru, $response->json('data.createDeliveryPoint.description_ru'));
        $this->assertEquals($deliveryPoint->description_en, $response->json('data.createDeliveryPoint.description_en'));
        $this->assertEquals($deliveryPoint->description_oz, $response->json('data.createDeliveryPoint.description_oz'));
        $this->assertEquals($deliveryPoint->is_prepaid, $response->json('data.createDeliveryPoint.is_prepaid'));
        $this->assertEquals($deliveryPoint->address_id, $response->json('data.createDeliveryPoint.address_id'));
        $this->assertEquals($deliveryPoint->created_at->timestamp, $response->json('data.createDeliveryPoint.created_at'));
        $this->assertEquals($deliveryPoint->updated_at->timestamp, $response->json('data.createDeliveryPoint.updated_at'));
        $this->assertEquals($deliveryPoint->deleted_at?->timestamp, $response->json('data.createDeliveryPoint.deleted_at'));
    }
}
