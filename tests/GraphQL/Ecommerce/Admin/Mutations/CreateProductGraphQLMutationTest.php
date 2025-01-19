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

class CreateProductGraphQLMutationTest extends GraphQLTestCase
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
    public function successCreateProduct()
    {
        $variables = $this->getInputVariables();

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery($variables));

        $this->assertSuccessCreateProductResponse($response, $variables);
    }

    #[Test]
    public function successCreateProductWithOnlyRequiredAttributes()
    {
        $variables = $this->getInputVariables(true);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery($variables));

        $this->assertSuccessCreateProductResponse($response, $variables, true);
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

        // rule errors
        $variables = $this->getInputVariables(false, [
            'name_uz' => Str::random(256),
            'name_ru' => Str::random(256),
            'name_en' => Str::random(256),
            'name_oz' => Str::random(256),
            'category_id' => 999999999,
            'brand_id' => 999999999,
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
        $this->assertEquals(trans('validation.exists', ['attribute' => 'product.category id']), $response->json('errors.0.extensions.validation')['product.category_id'][0]);
        $this->assertEquals(trans('validation.exists', ['attribute' => 'product.brand id']), $response->json('errors.0.extensions.validation')['product.brand_id'][0]);
    }

    #[Test]
    public function errorUniqueSlugValidation(): void
    {
        /** @var Product $product */
        $product = Product::factory()->make(['name_en' => 'thi$ !s n@me en']);
        $product->slug = $product->name_en;
        $product->saveOrFail();

        $variables = $this->getInputVariables(true, ['name_en' => $product->name_en]);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery($variables));

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.unique', ['attribute' => 'name']), $response->json('errors.0.extensions.validation')['product.name_en'][0]);
    }

    #[Test]
    public function errorCreateProductWithInactiveCompany(): void
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
                mutation CreateProduct(
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
                    createProduct(
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

    private function assertSuccessCreateProductResponse(TestResponse $response, array $variables, bool $onlyRequired = false): void
    {
        $response->assertOk();

        if (! $onlyRequired) {
            $response->assertJsonStructure(['data' => ['createProduct' => $this->_structure]]);
        } else {
            $response->assertJsonStructure(['data' => ['createProduct' => Arr::except($this->_structure, ['brand'])]]);
        }

        $product = Product::query()->findOrFail($response->json('data.createProduct.id'));

        foreach ($variables as $key => $value) {
            if ($key === 'images') {
                foreach ($value as $item) {
                    $this->assertDatabaseHas('ecommerce_imageables', [
                        'imageable_type' => Product::class,
                        'imageable_id' => $product->id,
                        'image_id' => $item['image_id'],
                        'order' => $item['order'],
                    ]);
                }

                continue;
            }

            $this->assertEquals($product->{$key}, $value);
        }

        $this->assertEquals($product->id, $response->json('data.createProduct.id'));
        $this->assertEquals($product->name_uz, $response->json('data.createProduct.name_uz'));
        $this->assertEquals($product->name_ru, $response->json('data.createProduct.name_ru'));
        $this->assertEquals($product->name_en, $response->json('data.createProduct.name_en'));
        $this->assertEquals($product->name_oz, $response->json('data.createProduct.name_oz'));
        $this->assertEquals($product->slug, $response->json('data.createProduct.slug'));
        $this->assertEquals($product->is_active, $response->json('data.createProduct.is_active'));
        $this->assertEquals($product->category_id, $response->json('data.createProduct.category_id'));
        $this->assertEquals($product->cost, $response->json('data.createProduct.cost'));
        $this->assertEquals($product->actual_price, $response->json('data.createProduct.actual_price'));
        $this->assertEquals($product->created_at->timestamp, $response->json('data.createProduct.created_at'));
        $this->assertEquals($product->updated_at->timestamp, $response->json('data.createProduct.updated_at'));
        $this->assertEquals($product->deleted_at?->timestamp, $response->json('data.createProduct.deleted_at'));

        if (! $onlyRequired) {
            $this->assertEquals($product->brand_id, $response->json('data.createProduct.brand_id'));
            $this->assertEquals($product->old_price, $response->json('data.createProduct.old_price'));
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
