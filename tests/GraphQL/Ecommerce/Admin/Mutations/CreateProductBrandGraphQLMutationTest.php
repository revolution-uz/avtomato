<?php

namespace Tests\GraphQL\Ecommerce\Admin\Mutations;

use App\Enums\Company\CompanyStatusEnum;
use App\Models\Company\Ecommerce\EcommerceImage;
use App\Models\Company\Ecommerce\ProductBrand;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class CreateProductBrandGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private array $_structure = [
        'id',
        'name_uz',
        'name_ru',
        'name_en',
        'name_oz',
        'slug',
        'is_active',
        'image_id',
        'created_at',
        'updated_at',
        'deleted_at',
        'image' => [
            'id',
            'url',
            'original_name',
            'mime',
            'order',
            'created_at',
            'updated_at',
            'deleted_at',
        ],
    ];

    #[Test]
    public function successCreateProductBrand()
    {
        $variables = $this->getInputVariables();

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery($variables));

        $this->assertSuccessCreateProductBrandResponse($response, $variables);
    }

    #[Test]
    public function successCreateProductBrandWithOnlyRequiredAttributes()
    {
        $variables = $this->getInputVariables(true);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery($variables));

        $this->assertSuccessCreateProductBrandResponse($response, $variables, true);
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
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$is_active" of required type "Boolean!" was not provided.'));

        // Rule errors
        $variables = $this->getInputVariables(false, [
            'name_uz' => Str::random(256),
            'name_ru' => Str::random(256),
            'name_en' => Str::random(256),
            'name_oz' => Str::random(256),
            'image_id' => 999999999,
        ]);
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery($variables));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'product brand.name uz', 'max' => 255]), $response->json('errors.0.extensions.validation')['productBrand.name_uz'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'product brand.name ru', 'max' => 255]), $response->json('errors.0.extensions.validation')['productBrand.name_ru'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'product brand.name en', 'max' => 255]), $response->json('errors.0.extensions.validation')['productBrand.name_en'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'product brand.name oz', 'max' => 255]), $response->json('errors.0.extensions.validation')['productBrand.name_oz'][0]);
        $this->assertEquals(trans('validation.exists', ['attribute' => 'product brand.image id']), $response->json('errors.0.extensions.validation')['productBrand.image_id'][0]);
    }

    #[Test]
    public function errorUniqueSlugValidation(): void
    {
        /** @var ProductBrand $brand */
        $brand = ProductBrand::factory()->make(['name_en' => 'thi$ !s n@me en']);
        $brand->slug = $brand->name_en;
        $brand->saveOrFail();

        $variables = $this->getInputVariables(true, ['name_en' => $brand->name_en]);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery($variables));

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.unique', ['attribute' => 'name']), $response->json('errors.0.extensions.validation')['productBrand.name_en'][0]);
    }

    #[Test]
    public function errorCreateProductBrandWithInactiveCompany(): void
    {
        tenant()->updateOrFail(['status' => CompanyStatusEnum::INACTIVE]);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery($this->getInputVariables()));

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals(trans('api.inactive_company'), $response->json('errors.0.message'));
    }

    protected function buildGraphQLQuery(?array $variables = null): array
    {
        return [
            'query' => <<<GRAPHQL
                mutation CreateProductBrand(
                    \$name_uz: String!
                    \$name_ru: String!
                    \$name_en: String!
                    \$name_oz: String!
                    \$is_active: Boolean!
                    \$image_id: Int
                ) {
                    createProductBrand(
                        productBrand: {
                            name_uz: \$name_uz
                            name_ru: \$name_ru
                            name_en: \$name_en
                            name_oz: \$name_oz
                            is_active: \$is_active
                            image_id: \$image_id
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
                'is_active' => (bool) random_int(0, 1),
                'image_id' => ! $isRequired ? EcommerceImage::factory()->create()->id : null,
            ],
            $variables
        );
    }

    private function assertSuccessCreateProductBrandResponse(TestResponse $response, array $variables, bool $onlyRequired = false): void
    {
        $response->assertOk();
        ! $onlyRequired ?
            $response->assertJsonStructure(['data' => ['createProductBrand' => $this->_structure]]) :
            $response->assertJsonStructure(['data' => ['createProductBrand' => Arr::except($this->_structure, ['image'])]]);

        $brand = ProductBrand::query()->findOrFail($response->json('data.createProductBrand.id'));

        $this->assertEquals($brand->name_uz, $variables['name_uz']);
        $this->assertEquals($brand->name_ru, $variables['name_ru']);
        $this->assertEquals($brand->name_en, $variables['name_en']);
        $this->assertEquals($brand->name_oz, $variables['name_oz']);
        $this->assertEquals($brand->is_active, $variables['is_active']);
        $this->assertEquals($brand->slug, Str::slug($variables['name_en']));
        ! $onlyRequired ?
            $this->assertEquals($brand->image_id, $variables['image_id']) :
            $this->assertNull($brand->image_id);

        $this->assertEquals($brand->id, $response->json('data.createProductBrand.id'));
        $this->assertEquals($brand->name_uz, $response->json('data.createProductBrand.name_uz'));
        $this->assertEquals($brand->name_ru, $response->json('data.createProductBrand.name_ru'));
        $this->assertEquals($brand->name_en, $response->json('data.createProductBrand.name_en'));
        $this->assertEquals($brand->name_oz, $response->json('data.createProductBrand.name_oz'));
        $this->assertEquals($brand->slug, $response->json('data.createProductBrand.slug'));
        $this->assertEquals($brand->is_active, $response->json('data.createProductBrand.is_active'));
        $this->assertEquals($brand->image_id, $response->json('data.createProductBrand.image_id'));
        $this->assertEquals($brand->created_at->timestamp, $response->json('data.createProductBrand.created_at'));
        $this->assertEquals($brand->updated_at->timestamp, $response->json('data.createProductBrand.updated_at'));
        $this->assertEquals($brand->deleted_at?->timestamp, $response->json('data.createProductBrand.deleted_at'));
    }
}
