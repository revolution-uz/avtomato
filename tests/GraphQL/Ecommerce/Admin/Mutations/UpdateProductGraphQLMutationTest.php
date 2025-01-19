<?php

namespace Tests\GraphQL\Ecommerce\Admin\Mutations;

use App\Enums\Company\CompanyStatusEnum;
use App\Models\Company\Ecommerce\EcommerceImage;
use App\Models\Company\Ecommerce\Product;
use App\Models\Company\Ecommerce\ProductBrand;
use App\Models\Company\Ecommerce\ProductCategory;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class UpdateProductGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private array $_structure = [
        'id',
        'name',
        'name_uz',
        'name_ru',
        'name_en',
        'name_oz',
        'slug',
        'cost',
        'actual_price',
        'old_price',
        'is_active',
        'category_id',
        'brand_id',
        'created_at',
        'updated_at',
        'deleted_at',
        'category' => [
            'id',
            'parent_id',
            'name_uz',
            'name_ru',
            'name_en',
            'name_oz',
            'is_active',
            'image_id',
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
        'images' => [
            '*' => [
                'id',
                'url',
                'original_name',
                'mime',
                'order',
                'created_at',
                'updated_at',
                'deleted_at',
            ],
        ],
    ];

    #[Test]
    public function successUpdateProduct()
    {
        /** @var Product $product */
        $product = Product::factory()->make();
        $product->slug = $product->name_en;
        $product->saveOrFail();
        $variables = $this->getInputVariables(false, ['id' => $product->id]);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery($variables));

        $this->assertSuccessUpdateProductResponse($response, $product, $variables);
    }

    #[Test]
    public function successUpdateProductWithOnlyRequiredAttributes()
    {
        /** @var Product $product */
        $product = Product::factory()->create();
        $variables = $this->getInputVariables(true, ['id' => $product->id]);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery($variables));

        $this->assertSuccessUpdateProductResponse($response, $product, $variables, true);
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
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$id" of required type "Int!" was not provided.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$name_uz" of required type "String!" was not provided.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$name_ru" of required type "String!" was not provided.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$name_en" of required type "String!" was not provided.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$name_oz" of required type "String!" was not provided.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$cost" of required type "Float!" was not provided.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$actual_price" of required type "Float!" was not provided.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$is_active" of required type "Boolean!" was not provided.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$category_id" of required type "Int!" was not provided.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$images" of required type "[ProductImageInputGraphQLType!]!" was not provided.'));

        // Rule errors
        /** @var Product $product */
        $product = Product::factory()->create();
        $variables = $this->getInputVariables(false, [
            'id' => $product->id,
            'name_uz' => Str::random(256),
            'name_ru' => Str::random(256),
            'name_en' => Str::random(256),
            'name_oz' => Str::random(256),
            'cost' => -1,
            'actual_price' => -1,
            'old_price' => -1,
            'category_id' => 999999999,
            'brand_id' => 999999999,
            'images' => [
                [
                    'image_id' => 999999999,
                    'order' => -1,
                ],
            ],
        ]);
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery($variables));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'product.name uz', 'max' => 255]), $response->json('errors.0.extensions.validation')['product.name_uz'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'product.name ru', 'max' => 255]), $response->json('errors.0.extensions.validation')['product.name_ru'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'product.name en', 'max' => 255]), $response->json('errors.0.extensions.validation')['product.name_en'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'product.name oz', 'max' => 255]), $response->json('errors.0.extensions.validation')['product.name_oz'][0]);
        $this->assertEquals(trans('validation.min.numeric', ['attribute' => 'product.cost', 'min' => 0]), $response->json('errors.0.extensions.validation')['product.cost'][0]);
        $this->assertEquals(trans('validation.min.numeric', ['attribute' => 'product.actual price', 'min' => 0]), $response->json('errors.0.extensions.validation')['product.actual_price'][0]);
        $this->assertEquals(trans('validation.min.numeric', ['attribute' => 'product.old price', 'min' => 0]), $response->json('errors.0.extensions.validation')['product.old_price'][0]);
        $this->assertEquals(trans('validation.exists', ['attribute' => 'product.category id']), $response->json('errors.0.extensions.validation')['product.category_id'][0]);
        $this->assertEquals(trans('validation.exists', ['attribute' => 'product.brand id']), $response->json('errors.0.extensions.validation')['product.brand_id'][0]);
        $this->assertEquals(trans('validation.exists', ['attribute' => 'product.images.0.image id']), $response->json('errors.0.extensions.validation')['product.images.0.image_id'][0]);
        $this->assertEquals(trans('validation.min.numeric', ['attribute' => 'product.images.0.order', 'min' => 0]), $response->json('errors.0.extensions.validation')['product.images.0.order'][0]);
    }

    #[Test]
    public function errorUniqueSlugValidation(): void
    {
        /** @var Product $existProduct */
        $existProduct = Product::factory()->make(['name_en' => 'thi$ !s n@me en']);
        $existProduct->slug = $existProduct->name_en;
        $existProduct->saveOrFail();
        /** @var Product $product */
        $product = Product::factory()->create();
        $variables = $this->getInputVariables(true, ['id' => $product->id, 'name_en' => $existProduct->name_en]);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery($variables));

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.unique', ['attribute' => 'name']), $response->json('errors.0.extensions.validation')['product.name_en'][0]);
    }

    #[Test]
    public function errorUpdateProductWithInactiveCompany(): void
    {
        /** @var Product $product */
        $product = Product::factory()->create();
        $variables = $this->getInputVariables(false, ['id' => $product->id]);

        tenant()->updateOrFail(['status' => CompanyStatusEnum::INACTIVE]);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery($variables));

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals(trans('api.inactive_company'), $response->json('errors.0.message'));
    }

    protected function buildGraphQLQuery(array $variables = []): array
    {
        return [
            'query' => <<<GRAPHQL
                mutation UpdateProduct(
                    \$id: Int!
                    \$name_uz: String!
                    \$name_ru: String!
                    \$name_en: String!
                    \$name_oz: String!
                    \$cost: Float!
                    \$actual_price: Float!
                    \$old_price: Float
                    \$is_active: Boolean!
                    \$category_id: Int!
                    \$brand_id: Int
                    \$images: [ProductImageInputGraphQLType!]!
                ) {
                    updateProduct(
                        id: \$id
                        product: {
                            name_uz: \$name_uz
                            name_ru: \$name_ru
                            name_en: \$name_en
                            name_oz: \$name_oz
                            cost: \$cost
                            actual_price: \$actual_price
                            old_price: \$old_price
                            is_active: \$is_active
                            category_id: \$category_id
                            brand_id: \$brand_id
                            images: \$images
                        }
                    ) {
                        {$this->convertIntoQuery($this->_structure)}
                    }
                }
            GRAPHQL,
            'variables' => $variables,
        ];
    }

    private function assertSuccessUpdateProductResponse(TestResponse $response, Product $product, array $variables, bool $onlyRequired = false): void
    {
        $response->assertOk();
        ! $onlyRequired ?
            $response->assertJsonStructure(['data' => ['updateProduct' => $this->_structure]]) :
            $response->assertJsonStructure(['data' => ['updateProduct' => Arr::except($this->_structure, ['brand'])]]);

        $product->refresh();

        $this->assertEquals($product->name_uz, $variables['name_uz']);
        $this->assertEquals($product->name_ru, $variables['name_ru']);
        $this->assertEquals($product->name_en, $variables['name_en']);
        $this->assertEquals($product->name_oz, $variables['name_oz']);
        $this->assertEquals($product->cost, $variables['cost']);
        $this->assertEquals($product->actual_price, $variables['actual_price']);
        $this->assertEquals($product->old_price, $variables['old_price']);
        $this->assertEquals($product->is_active, $variables['is_active']);
        $this->assertEquals($product->category_id, $variables['category_id']);
        $this->assertEquals($product->brand_id, $variables['brand_id']);
        $this->assertEquals(
            $product->images()->whereIn('image_id', collect($variables['images'])->pluck('image_id'))->count(),
            count($variables['images'])
        );

        $this->assertEquals($product->id, $response->json('data.updateProduct.id'));
        $this->assertEquals($product->name_uz, $response->json('data.updateProduct.name_uz'));
        $this->assertEquals($product->name_ru, $response->json('data.updateProduct.name_ru'));
        $this->assertEquals($product->name_en, $response->json('data.updateProduct.name_en'));
        $this->assertEquals($product->name_oz, $response->json('data.updateProduct.name_oz'));
        $this->assertEquals($product->slug, $response->json('data.updateProduct.slug'));
        $this->assertEquals($product->is_active, $response->json('data.updateProduct.is_active'));
        $this->assertEquals($product->category_id, $response->json('data.updateProduct.category_id'));
        $this->assertEquals($product->cost, $response->json('data.updateProduct.cost'));
        $this->assertEquals($product->actual_price, $response->json('data.updateProduct.actual_price'));
        $this->assertEquals($product->created_at->timestamp, $response->json('data.updateProduct.created_at'));
        $this->assertEquals($product->updated_at->timestamp, $response->json('data.updateProduct.updated_at'));
        $this->assertEquals($product->deleted_at?->timestamp, $response->json('data.updateProduct.deleted_at'));
        if (! $onlyRequired) {
            $this->assertEquals($product->brand_id, $response->json('data.updateProduct.brand_id'));
            $this->assertEquals($product->old_price, $response->json('data.updateProduct.old_price'));
        } else {
            $this->assertNull($product->brand_id);
            $this->assertNull($product->old_price);
        }
    }

    private function getInputVariables(bool $isRequired = false, array $variables = [])
    {
        /** @var EcommerceImage[] $images */
        $images = EcommerceImage::factory()->count(2)->create();

        return array_merge(
            [
                'name_uz' => Str::random(255),
                'name_ru' => Str::random(255),
                'name_en' => Str::random(255),
                'name_oz' => Str::random(255),
                'cost' => random_int(1, 999999999) / 100,
                'actual_price' => random_int(1, 999999999) / 100,
                'old_price' => ! $isRequired ? random_int(1, 999999999) / 100 : null,
                'is_active' => (bool) rand(0, 1),
                'category_id' => ProductCategory::factory()->create()->id,
                'brand_id' => ! $isRequired ? ProductBrand::factory()->create()->id : null,
                'images' => [
                    [
                        'image_id' => $images[1]->id,
                        'order' => 1,
                    ],
                    [
                        'image_id' => $images[0]->id,
                        'order' => 2,
                    ],
                ],
            ],
            $variables
        );
    }
}
