<?php

namespace Tests\GraphQL\Ecommerce\Admin\Mutations;

use App\Enums\Company\CompanyStatusEnum;
use App\Models\Company\District;
use App\Models\Company\Ecommerce\DeliveryPoint;
use App\Models\Company\Mahalla;
use App\Models\Company\Region;
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
        'phone',
        'address_uz',
        'address_ru',
        'address_en',
        'address_oz',
        'landmark_uz',
        'landmark_ru',
        'landmark_en',
        'landmark_oz',
        'location_url',
        'lat',
        'lng',
        'region_id',
        'district_id',
        'mahalla_id',
        'created_at',
        'updated_at',
        'deleted_at',
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
    public function successCreateDeliveryPointWithOnlyRequiredFields()
    {
        $variables = $this->getInputVariables(true);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery($variables));

        $this->assertSuccessCreateDeliveryPointResponse($response, $variables, true);
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
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$phone" of required type "String!" was not provided.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$address_uz" of required type "String!" was not provided.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$address_ru" of required type "String!" was not provided.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$address_en" of required type "String!" was not provided.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$address_oz" of required type "String!" was not provided.'));

        // Rule errors
        $variables = $this->getInputVariables(variables: [
            'name_uz' => Str::random(256),
            'name_ru' => Str::random(256),
            'name_en' => Str::random(256),
            'name_oz' => Str::random(256),
            'description_uz' => Str::random(256),
            'description_ru' => Str::random(256),
            'description_en' => Str::random(256),
            'description_oz' => Str::random(256),
            'phone' => 'abc',
            'address_uz' => Str::random(256),
            'address_ru' => Str::random(256),
            'address_en' => Str::random(256),
            'address_oz' => Str::random(256),
            'landmark_uz' => Str::random(256),
            'landmark_ru' => Str::random(256),
            'landmark_en' => Str::random(256),
            'landmark_oz' => Str::random(256),
            'location_url' => Str::random(256),
            'region_id' => 999999999,
            'district_id' => 999999999,
            'mahalla_id' => 999999999,
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
        $this->assertEquals(trans('validation.regex', ['attribute' => 'delivery point.phone']), $response->json('errors.0.extensions.validation')['deliveryPoint.phone'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'delivery point.address uz', 'max' => 255]), $response->json('errors.0.extensions.validation')['deliveryPoint.address_uz'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'delivery point.address ru', 'max' => 255]), $response->json('errors.0.extensions.validation')['deliveryPoint.address_ru'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'delivery point.address en', 'max' => 255]), $response->json('errors.0.extensions.validation')['deliveryPoint.address_en'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'delivery point.address oz', 'max' => 255]), $response->json('errors.0.extensions.validation')['deliveryPoint.address_oz'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'delivery point.landmark uz', 'max' => 255]), $response->json('errors.0.extensions.validation')['deliveryPoint.landmark_uz'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'delivery point.landmark ru', 'max' => 255]), $response->json('errors.0.extensions.validation')['deliveryPoint.landmark_ru'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'delivery point.landmark en', 'max' => 255]), $response->json('errors.0.extensions.validation')['deliveryPoint.landmark_en'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'delivery point.landmark oz', 'max' => 255]), $response->json('errors.0.extensions.validation')['deliveryPoint.landmark_oz'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'delivery point.location url', 'max' => 255]), $response->json('errors.0.extensions.validation')['deliveryPoint.location_url'][0]);
        $this->assertEquals(trans('validation.exists', ['attribute' => 'delivery point.region id']), $response->json('errors.0.extensions.validation')['deliveryPoint.region_id'][0]);
        $this->assertEquals(trans('validation.exists', ['attribute' => 'delivery point.district id']), $response->json('errors.0.extensions.validation')['deliveryPoint.district_id'][0]);
        $this->assertEquals(trans('validation.exists', ['attribute' => 'delivery point.mahalla id']), $response->json('errors.0.extensions.validation')['deliveryPoint.mahalla_id'][0]);
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
                    \$phone: String!
                    \$address_uz: String!
                    \$address_ru: String!
                    \$address_en: String!
                    \$address_oz: String!
                    \$landmark_uz: String
                    \$landmark_ru: String
                    \$landmark_en: String
                    \$landmark_oz: String
                    \$location_url: String
                    \$lat: Float
                    \$lng: Float
                    \$region_id: Int
                    \$district_id: Int
                    \$mahalla_id: Int
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
                            phone: \$phone
                            address_uz: \$address_uz
                            address_ru: \$address_ru
                            address_en: \$address_en
                            address_oz: \$address_oz
                            landmark_uz: \$landmark_uz
                            landmark_ru: \$landmark_ru
                            landmark_en: \$landmark_en
                            landmark_oz: \$landmark_oz
                            location_url: \$location_url
                            lat: \$lat
                            lng: \$lng
                            region_id: \$region_id
                            district_id: \$district_id
                            mahalla_id: \$mahalla_id
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
                'name_uz' => Str::random(255),
                'name_ru' => Str::random(255),
                'name_en' => Str::random(255),
                'name_oz' => Str::random(255),
                'description_uz' => Str::random(255),
                'description_ru' => Str::random(255),
                'description_en' => Str::random(255),
                'description_oz' => Str::random(255),
                'is_prepaid' => (bool) random_int(0, 1),
                'phone' => '+998' . random_int(111111111, 999999999),
                'address_uz' => Str::random(255),
                'address_ru' => Str::random(255),
                'address_en' => Str::random(255),
                'address_oz' => Str::random(255),
                'landmark_uz' => ! $isRequired ? Str::random(255) : null,
                'landmark_ru' => ! $isRequired ? Str::random(255) : null,
                'landmark_en' => ! $isRequired ? Str::random(255) : null,
                'landmark_oz' => ! $isRequired ? Str::random(255) : null,
                'location_url' => ! $isRequired ? Str::random(255) : null,
                'lat' => ! $isRequired ? random_int(0, 99999) / 1000 : null,
                'lng' => ! $isRequired ? random_int(0, 99999) / 1000 : null,
                'region_id' => ! $isRequired ? Region::factory()->create()->id : null,
                'district_id' => ! $isRequired ? District::factory()->create()->id : null,
                'mahalla_id' => ! $isRequired ? Mahalla::factory()->create()->id : null,
            ],
            $variables
        );
    }

    private function assertSuccessCreateDeliveryPointResponse(TestResponse $response, array $variables, bool $isRequired = false): void
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
        $this->assertEquals($deliveryPoint->phone, $variables['phone']);
        $this->assertEquals($deliveryPoint->address_uz, $variables['address_uz']);
        $this->assertEquals($deliveryPoint->address_ru, $variables['address_ru']);
        $this->assertEquals($deliveryPoint->address_en, $variables['address_en']);
        $this->assertEquals($deliveryPoint->address_oz, $variables['address_oz']);

        if (! $isRequired) {
            $this->assertEquals($deliveryPoint->landmark_uz, $variables['landmark_uz']);
            $this->assertEquals($deliveryPoint->landmark_ru, $variables['landmark_ru']);
            $this->assertEquals($deliveryPoint->landmark_en, $variables['landmark_en']);
            $this->assertEquals($deliveryPoint->landmark_oz, $variables['landmark_oz']);
            $this->assertEquals($deliveryPoint->location_url, $variables['location_url']);
            $this->assertEquals($deliveryPoint->lat, $variables['lat']);
            $this->assertEquals($deliveryPoint->lng, $variables['lng']);
            $this->assertEquals($deliveryPoint->region_id, $variables['region_id']);
            $this->assertEquals($deliveryPoint->district_id, $variables['district_id']);
            $this->assertEquals($deliveryPoint->mahalla_id, $variables['mahalla_id']);
        } else {
            $this->assertNull($deliveryPoint->landmark_uz);
            $this->assertNull($deliveryPoint->landmark_ru);
            $this->assertNull($deliveryPoint->landmark_en);
            $this->assertNull($deliveryPoint->landmark_oz);
            $this->assertNull($deliveryPoint->location_url);
            $this->assertNull($deliveryPoint->lat);
            $this->assertNull($deliveryPoint->lng);
            $this->assertNull($deliveryPoint->region_id);
            $this->assertNull($deliveryPoint->district_id);
            $this->assertNull($deliveryPoint->mahalla_id);
        }

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
        $this->assertEquals($deliveryPoint->phone, $response->json('data.createDeliveryPoint.phone'));
        $this->assertEquals($deliveryPoint->address_uz, $response->json('data.createDeliveryPoint.address_uz'));
        $this->assertEquals($deliveryPoint->address_ru, $response->json('data.createDeliveryPoint.address_ru'));
        $this->assertEquals($deliveryPoint->address_en, $response->json('data.createDeliveryPoint.address_en'));
        $this->assertEquals($deliveryPoint->address_oz, $response->json('data.createDeliveryPoint.address_oz'));
        $this->assertEquals($deliveryPoint->landmark_uz, $response->json('data.createDeliveryPoint.landmark_uz'));
        $this->assertEquals($deliveryPoint->landmark_ru, $response->json('data.createDeliveryPoint.landmark_ru'));
        $this->assertEquals($deliveryPoint->landmark_en, $response->json('data.createDeliveryPoint.landmark_en'));
        $this->assertEquals($deliveryPoint->landmark_oz, $response->json('data.createDeliveryPoint.landmark_oz'));
        $this->assertEquals($deliveryPoint->location_url, $response->json('data.createDeliveryPoint.location_url'));
        $this->assertEquals($deliveryPoint->lat, $response->json('data.createDeliveryPoint.lat'));
        $this->assertEquals($deliveryPoint->lng, $response->json('data.createDeliveryPoint.lng'));
        $this->assertEquals($deliveryPoint->region_id, $response->json('data.createDeliveryPoint.region_id'));
        $this->assertEquals($deliveryPoint->district_id, $response->json('data.createDeliveryPoint.district_id'));
        $this->assertEquals($deliveryPoint->mahalla_id, $response->json('data.createDeliveryPoint.mahalla_id'));
        $this->assertEquals($deliveryPoint->created_at->timestamp, $response->json('data.createDeliveryPoint.created_at'));
        $this->assertEquals($deliveryPoint->updated_at->timestamp, $response->json('data.createDeliveryPoint.updated_at'));
        $this->assertEquals($deliveryPoint->deleted_at?->timestamp, $response->json('data.createDeliveryPoint.deleted_at'));
    }
}
