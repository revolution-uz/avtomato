<?php

namespace Tests\GraphQL\Ecommerce\Admin\Mutations;

use App\Enums\Company\CompanyStatusEnum;
use App\Models\Company\Ecommerce\EcommerceImage;
use App\Models\Company\Ecommerce\ProductCategory;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class UpdateProductCategoryGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private array $_structure = [
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
        'image' => [
            'id',
            'url',
            'original_name',
            'mime',
            'created_at',
            'updated_at',
            'deleted_at',
        ],
        'parent' => [
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
        'products' => [
            '*' => [
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
        ],
        'banners' => [
            '*' => [
                'id',
                'name_uz',
                'name_ru',
                'name_en',
                'name_oz',
                'image_uz_id',
                'image_ru_id',
                'image_en_id',
                'image_oz_id',
                'is_active',
                'url',
                'category_id',
                'brand_id',
                'product_id',
                'created_at',
                'updated_at',
                'deleted_at',
            ],
        ],
    ];

    #[Test]
    public function successUpdateProductCategory()
    {
        /** @var ProductCategory $productCategory */
        $productCategory = ProductCategory::factory()->create();
        $variables = $this->getInputVariables(variables: ['id' => $productCategory->id]);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->post(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery($variables));

        $response->assertOk();
        $response->assertJsonStructure(['data' => ['updateProductCategory' => $this->_structure]]);

        $productCategory->refresh();
        $this->assertEquals($productCategory->parent_id, $variables['parent_id']);
        $this->assertEquals($productCategory->name_uz, $variables['name_uz']);
        $this->assertEquals($productCategory->name_ru, $variables['name_ru']);
        $this->assertEquals($productCategory->name_en, $variables['name_en']);
        $this->assertEquals($productCategory->name_oz, $variables['name_oz']);
        $this->assertEquals($productCategory->is_active, $variables['is_active']);
        $this->assertEquals($productCategory->image_id, $variables['image_id']);
        $this->assertEquals($productCategory->parent_id, $response->json('data.updateProductCategory.parent_id'));
        $this->assertEquals($productCategory->name_uz, $response->json('data.updateProductCategory.name_uz'));
        $this->assertEquals($productCategory->name_ru, $response->json('data.updateProductCategory.name_ru'));
        $this->assertEquals($productCategory->name_en, $response->json('data.updateProductCategory.name_en'));
        $this->assertEquals($productCategory->name_oz, $response->json('data.updateProductCategory.name_oz'));
        $this->assertEquals($productCategory->is_active, $response->json('data.updateProductCategory.is_active'));
        $this->assertEquals($productCategory->image_id, $response->json('data.updateProductCategory.image_id'));
        $this->assertEquals($productCategory->created_at->timestamp, $response->json('data.updateProductCategory.created_at'));
        $this->assertEquals($productCategory->updated_at->timestamp, $response->json('data.updateProductCategory.updated_at'));
        $this->assertEquals($productCategory->deleted_at?->timestamp, $response->json('data.updateProductCategory.deleted_at'));
    }

    #[Test]
    public function successUpdateProductCategoryWithOnlyRequiredAttributes()
    {
        /** @var ProductCategory $productCategory */
        $productCategory = ProductCategory::factory()->create();
        $variables = $this->getInputVariables(true, ['id' => $productCategory->id]);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->post(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery($variables));

        $response->assertOk();
        $response->assertJsonStructure(['data' => ['updateProductCategory' => Arr::except($this->_structure, ['parent'])]]);

        $productCategory->refresh();
        $this->assertEquals($productCategory->name_uz, $variables['name_uz']);
        $this->assertEquals($productCategory->name_ru, $variables['name_ru']);
        $this->assertEquals($productCategory->name_en, $variables['name_en']);
        $this->assertEquals($productCategory->name_oz, $variables['name_oz']);
        $this->assertEquals($productCategory->is_active, $variables['is_active']);
        $this->assertEquals($productCategory->image_id, $variables['image_id']);
        $this->assertNull($productCategory->parent_id);
        $this->assertEquals($productCategory->name_uz, $response->json('data.updateProductCategory.name_uz'));
        $this->assertEquals($productCategory->name_ru, $response->json('data.updateProductCategory.name_ru'));
        $this->assertEquals($productCategory->name_en, $response->json('data.updateProductCategory.name_en'));
        $this->assertEquals($productCategory->name_oz, $response->json('data.updateProductCategory.name_oz'));
        $this->assertEquals($productCategory->is_active, $response->json('data.updateProductCategory.is_active'));
        $this->assertEquals($productCategory->image_id, $response->json('data.updateProductCategory.image_id'));
        $this->assertNull($response->json('data.updateProductCategory.parent_id'));
        $this->assertEquals($productCategory->created_at->timestamp, $response->json('data.updateProductCategory.created_at'));
        $this->assertEquals($productCategory->updated_at->timestamp, $response->json('data.updateProductCategory.updated_at'));
        $this->assertEquals($productCategory->deleted_at?->timestamp, $response->json('data.updateProductCategory.deleted_at'));
    }

    #[Test]
    public function errorValidation(): void
    {
        // GraphQL type errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->post(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery());
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$id" of required type "Int!" was not provided.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$name_uz" of required type "String!" was not provided.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$name_ru" of required type "String!" was not provided.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$name_en" of required type "String!" was not provided.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$name_oz" of required type "String!" was not provided.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$is_active" of required type "Boolean!" was not provided.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$image_id" of required type "Int!" was not provided.'));

        // rule errors
        $variables = $this->getInputVariables(true, [
            'id' => 999999999,
            'parent_id' => 999999999,
            'name_uz' => Str::random(256),
            'name_ru' => Str::random(256),
            'name_en' => Str::random(256),
            'name_oz' => Str::random(256),
            'image_id' => 999999999,
        ]);
        $response = $this
            ->actingAs($this->createAdminUser())
            ->post(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery($variables));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'id']), $response->json('errors.0.extensions.validation.id.0'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'product category.parent id']), $response->json('errors.0.extensions.validation')['productCategory.parent_id'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'product category.name uz', 'max' => 255]), $response->json('errors.0.extensions.validation')['productCategory.name_uz'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'product category.name ru', 'max' => 255]), $response->json('errors.0.extensions.validation')['productCategory.name_ru'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'product category.name en', 'max' => 255]), $response->json('errors.0.extensions.validation')['productCategory.name_en'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'product category.name oz', 'max' => 255]), $response->json('errors.0.extensions.validation')['productCategory.name_oz'][0]);
        $this->assertEquals(trans('validation.exists', ['attribute' => 'product category.image id']), $response->json('errors.0.extensions.validation')['productCategory.image_id'][0]);
    }

    #[Test]
    public function errorUpdateProductCategoryWithInactiveCompany(): void
    {
        /** @var ProductCategory $productCategory */
        $productCategory = ProductCategory::factory()->create();

        tenant()->updateOrFail(['status' => CompanyStatusEnum::INACTIVE]);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->post(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery($this->getInputVariables(true, ['id' => $productCategory->id])));

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals(trans('api.inactive_company'), $response->json('errors.0.message'));
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        return [
            'query' => <<<GRAPHQL
                mutation UpdateProductCategory(
                    \$id: Int!
                    \$parent_id: Int
                    \$name_uz: String!
                    \$name_ru: String!
                    \$name_en: String!
                    \$name_oz: String!
                    \$is_active: Boolean!
                    \$image_id: Int!
                ) {
                    updateProductCategory(
                        id: \$id
                        productCategory: {
                            parent_id: \$parent_id
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

    private function getInputVariables(bool $isRequired = false, array $variables = []): array
    {
        return array_merge(
            [
                'parent_id' => ! $isRequired ? ProductCategory::factory()->create()->id : null,
                'name_uz' => Str::random(255),
                'name_ru' => Str::random(255),
                'name_en' => Str::random(255),
                'name_oz' => Str::random(255),
                'is_active' => (bool) random_int(0, 1),
                'image_id' => EcommerceImage::factory()->create()->id,
            ],
            $variables
        );
    }
}
