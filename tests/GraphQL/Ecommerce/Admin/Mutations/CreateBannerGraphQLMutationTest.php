<?php

namespace Tests\GraphQL\Ecommerce\Admin\Mutations;

use App\Enums\Company\CompanyStatusEnum;
use App\Models\Company\Ecommerce\Banner;
use App\Models\Company\Ecommerce\EcommerceImage;
use App\Models\Company\Ecommerce\Product;
use App\Models\Company\Ecommerce\ProductBrand;
use App\Models\Company\Ecommerce\ProductCategory;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class CreateBannerGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private array $_structure = [
        'id',
        'name_uz',
        'name_ru',
        'name_en',
        'name_oz',
        'is_active',
        'url',
        'category_id',
        'brand_id',
        'product_id',
        'created_at',
        'updated_at',
        'deleted_at',
        'image_uz' => [
            'id',
            'url',
            'original_name',
            'mime',
            'created_at',
            'updated_at',
            'deleted_at',
        ],
        'image_ru' => [
            'id',
            'url',
            'original_name',
            'mime',
            'created_at',
            'updated_at',
            'deleted_at',
        ],
        'image_en' => [
            'id',
            'url',
            'original_name',
            'mime',
            'created_at',
            'updated_at',
            'deleted_at',
        ],
        'image_oz' => [
            'id',
            'url',
            'original_name',
            'mime',
            'created_at',
            'updated_at',
            'deleted_at',
        ],
        'category' => [
            'id',
            'parent_id',
            'name_uz',
            'name_ru',
            'name_en',
            'name_oz',
            'is_active',
            'created_at',
            'updated_at',
            'deleted_at',
        ],
        'brand' => [
            'id',
            'name_uz',
            'name_ru',
            'name_en',
            'name_oz',
            'is_active',
            'created_at',
            'updated_at',
            'deleted_at',
        ],
        'product' => [
            'id',
            'category_id',
            'brand_id',
            'name_uz',
            'name_ru',
            'name_en',
            'name_oz',
            'slug',
            'is_active',
            'cost',
            'actual_price',
            'old_price',
            'created_at',
            'updated_at',
            'deleted_at',
        ],
    ];

    #[Test]
    public function successCreateBanner()
    {
        $variables = $this->getInputVariables();

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery($variables));

        $response->assertOk();
        $response->assertJsonStructure(['data' => ['createBanner' => $this->_structure]]);

        $banner = Banner::query()->with(['imageUz', 'imageRu', 'imageEn', 'imageOz'])->findOrFail($response->json('data.createBanner.id'));

        $this->assertEquals($banner->name_uz, $variables['name_uz']);
        $this->assertEquals($banner->name_ru, $variables['name_ru']);
        $this->assertEquals($banner->name_en, $variables['name_en']);
        $this->assertEquals($banner->name_oz, $variables['name_oz']);
        $this->assertEquals($banner->is_active, $variables['is_active']);
        $this->assertEquals($banner->url, $variables['url']);
        $this->assertEquals($banner->category_id, $variables['category_id']);
        $this->assertEquals($banner->brand_id, $variables['brand_id']);
        $this->assertEquals($banner->product_id, $variables['product_id']);
        $this->assertEquals($banner->imageUz->id, $variables['image_uz_id']);
        $this->assertEquals($banner->imageRu->id, $variables['image_ru_id']);
        $this->assertEquals($banner->imageEn->id, $variables['image_en_id']);
        $this->assertEquals($banner->imageOz->id, $variables['image_oz_id']);

        $this->assertEquals($banner->name_uz, $response->json('data.createBanner.name_uz'));
        $this->assertEquals($banner->name_ru, $response->json('data.createBanner.name_ru'));
        $this->assertEquals($banner->name_en, $response->json('data.createBanner.name_en'));
        $this->assertEquals($banner->name_oz, $response->json('data.createBanner.name_oz'));
        $this->assertEquals($banner->is_active, $response->json('data.createBanner.is_active'));
        $this->assertEquals($banner->url, $response->json('data.createBanner.url'));
        $this->assertEquals($banner->category_id, $response->json('data.createBanner.category_id'));
        $this->assertEquals($banner->brand_id, $response->json('data.createBanner.brand_id'));
        $this->assertEquals($banner->product_id, $response->json('data.createBanner.product_id'));
        $this->assertEquals($banner->created_at->timestamp, $response->json('data.createBanner.created_at'));
        $this->assertEquals($banner->updated_at->timestamp, $response->json('data.createBanner.updated_at'));
        $this->assertEquals($banner->deleted_at?->timestamp, $response->json('data.createBanner.deleted_at'));
    }

    #[Test]
    public function successCreateBannerWithOnlyRequiredAttributes()
    {
        $variables = $this->getInputVariables(true);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery($variables));

        $response->assertOk();
        $response->assertJsonStructure(['data' => ['createBanner' => Arr::except($this->_structure, ['category', 'brand', 'product'])]]);

        $banner = Banner::query()->with(['imageUz', 'imageRu', 'imageEn', 'imageOz'])->findOrFail($response->json('data.createBanner.id'));

        $this->assertEquals($banner->name_uz, $variables['name_uz']);
        $this->assertEquals($banner->name_ru, $variables['name_ru']);
        $this->assertEquals($banner->name_en, $variables['name_en']);
        $this->assertEquals($banner->name_oz, $variables['name_oz']);
        $this->assertEquals($banner->is_active, $variables['is_active']);
        $this->assertNull($banner->url);
        $this->assertNull($banner->category_id);
        $this->assertNull($banner->brand_id);
        $this->assertNull($banner->product_id);
        $this->assertEquals($banner->imageUz->id, $variables['image_uz_id']);
        $this->assertEquals($banner->imageRu->id, $variables['image_ru_id']);
        $this->assertEquals($banner->imageEn->id, $variables['image_en_id']);
        $this->assertEquals($banner->imageOz->id, $variables['image_oz_id']);

        $this->assertEquals($banner->name_uz, $response->json('data.createBanner.name_uz'));
        $this->assertEquals($banner->name_ru, $response->json('data.createBanner.name_ru'));
        $this->assertEquals($banner->name_en, $response->json('data.createBanner.name_en'));
        $this->assertEquals($banner->name_oz, $response->json('data.createBanner.name_oz'));
        $this->assertEquals($banner->is_active, $response->json('data.createBanner.is_active'));
        $this->assertEquals($banner->url, $response->json('data.createBanner.url'));
        $this->assertEquals($banner->category_id, $response->json('data.createBanner.category_id'));
        $this->assertEquals($banner->brand_id, $response->json('data.createBanner.brand_id'));
        $this->assertEquals($banner->product_id, $response->json('data.createBanner.product_id'));
        $this->assertEquals($banner->created_at->timestamp, $response->json('data.createBanner.created_at'));
        $this->assertEquals($banner->updated_at->timestamp, $response->json('data.createBanner.updated_at'));
        $this->assertEquals($banner->deleted_at?->timestamp, $response->json('data.createBanner.deleted_at'));
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
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$image_uz_id" of required type "Int!" was not provided.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$image_ru_id" of required type "Int!" was not provided.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$image_en_id" of required type "Int!" was not provided.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$image_oz_id" of required type "Int!" was not provided.'));

        // rule errors
        $variables = $this->getInputVariables(false, [
            'name_uz' => Str::random(256),
            'name_ru' => Str::random(256),
            'name_en' => Str::random(256),
            'name_oz' => Str::random(256),
            'url' => Str::random(256),
            'image_uz_id' => 999999999,
            'image_ru_id' => 999999999,
            'image_en_id' => 999999999,
            'image_oz_id' => 999999999,
            'category_id' => 999999999,
            'brand_id' => 999999999,
            'product_id' => 999999999,
        ]);
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery($variables));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'banner.name uz', 'max' => 255]), $response->json('errors.0.extensions.validation')['banner.name_uz'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'banner.name ru', 'max' => 255]), $response->json('errors.0.extensions.validation')['banner.name_ru'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'banner.name en', 'max' => 255]), $response->json('errors.0.extensions.validation')['banner.name_en'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'banner.name oz', 'max' => 255]), $response->json('errors.0.extensions.validation')['banner.name_oz'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'banner.url', 'max' => 255]), $response->json('errors.0.extensions.validation')['banner.url'][0]);
        $this->assertEquals(trans('validation.exists', ['attribute' => 'banner.image uz id']), $response->json('errors.0.extensions.validation')['banner.image_uz_id'][0]);
        $this->assertEquals(trans('validation.exists', ['attribute' => 'banner.image ru id']), $response->json('errors.0.extensions.validation')['banner.image_ru_id'][0]);
        $this->assertEquals(trans('validation.exists', ['attribute' => 'banner.image en id']), $response->json('errors.0.extensions.validation')['banner.image_en_id'][0]);
        $this->assertEquals(trans('validation.exists', ['attribute' => 'banner.image oz id']), $response->json('errors.0.extensions.validation')['banner.image_oz_id'][0]);
        $this->assertEquals(trans('validation.exists', ['attribute' => 'banner.category id']), $response->json('errors.0.extensions.validation')['banner.category_id'][0]);
        $this->assertEquals(trans('validation.exists', ['attribute' => 'banner.brand id']), $response->json('errors.0.extensions.validation')['banner.brand_id'][0]);
        $this->assertEquals(trans('validation.exists', ['attribute' => 'banner.product id']), $response->json('errors.0.extensions.validation')['banner.product_id'][0]);
    }

    #[Test]
    public function errorCreateBannerWithInactiveCompany(): void
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
                mutation CreateBanner(
                    \$name_uz: String!
                    \$name_ru: String!
                    \$name_en: String!
                    \$name_oz: String!
                    \$url: String
                    \$is_active: Boolean!
                    \$image_uz_id: Int!
                    \$image_ru_id: Int!
                    \$image_en_id: Int!
                    \$image_oz_id: Int!
                    \$category_id: Int
                    \$brand_id: Int
                    \$product_id: Int
                ) {
                    createBanner(
                        banner: {
                            name_uz: \$name_uz
                            name_ru: \$name_ru
                            name_en: \$name_en
                            name_oz: \$name_oz
                            url: \$url
                            is_active: \$is_active
                            image_uz_id: \$image_uz_id
                            image_ru_id: \$image_ru_id
                            image_en_id: \$image_en_id
                            image_oz_id: \$image_oz_id
                            category_id: \$category_id
                            brand_id: \$brand_id
                            product_id: \$product_id
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
                'url' => ! $isRequired ? Str::random(255) : null,
                'is_active' => (bool) random_int(0, 1),
                'category_id' => ! $isRequired ? ProductCategory::factory()->create()->id : null,
                'brand_id' => ! $isRequired ? ProductBrand::factory()->create()->id : null,
                'product_id' => ! $isRequired ? Product::factory()->create()->id : null,
                'image_uz_id' => EcommerceImage::factory()->create()->id,
                'image_ru_id' => EcommerceImage::factory()->create()->id,
                'image_en_id' => EcommerceImage::factory()->create()->id,
                'image_oz_id' => EcommerceImage::factory()->create()->id,
            ],
            $variables
        );
    }
}
