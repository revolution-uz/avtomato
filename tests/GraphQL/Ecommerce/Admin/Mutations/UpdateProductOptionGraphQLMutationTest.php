<?php

namespace Tests\GraphQL\Ecommerce\Admin\Mutations;

use App\Enums\Company\CompanyStatusEnum;
use App\Enums\Company\OptionTypeEnum;
use App\Models\Company\Ecommerce\ProductCategory;
use App\Models\Company\Ecommerce\ProductOption;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class UpdateProductOptionGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private array $_structure = [
        'id',
        'name_uz',
        'name_ru',
        'name_en',
        'name_oz',
        'slug',
        'type',
        'description',
        'created_at',
        'updated_at',
        'deleted_at',
        'values' => [
            '*' => [
                'id',
                'value_uz',
                'value_ru',
                'value_en',
                'value_oz',
                'slug',
                'created_at',
                'updated_at',
                'deleted_at',
            ],
        ],
        'categories' => [
            '*' => [
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
        ],
    ];

    #[Test]
    public function successUpdateProductOption()
    {
        /** @var ProductOption $option */
        $option = ProductOption::factory()->make();
        $option->slug = $option->name_en;
        $option->saveOrFail();
        $variables = $this->getInputVariables(false, ['id' => $option->id]);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery($variables));

        $this->assertSuccessUpdateProductOptionResponse($response, $option, $variables);
    }

    #[Test]
    public function successUpdateProductOptionWithOnlyRequiredAttributes()
    {
        /** @var ProductOption $option */
        $option = ProductOption::factory()->create();
        $variables = $this->getInputVariables(true, ['id' => $option->id]);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery($variables));

        $this->assertSuccessUpdateProductOptionResponse($response, $option, $variables, true);
    }

    #[Test]
    public function successDetachingCategories()
    {
        // Detach all categories on null
        /** @var ProductOption $option */
        $option = ProductOption::factory()->create();
        /** @var Collection<ProductCategory, int> $categories */
        $categories = ProductCategory::factory()->count(5)->create();
        $option->categories()->attach($categories->pluck('id'));
        $variables = $this->getInputVariables(false, ['id' => $option->id, 'categories' => null]);

        $this->actingAs($this->createAdminUser())->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery($variables));

        $this->assertEquals(0, $option->categories()->count());

        // Detach all categories on empty array
        /** @var ProductOption $option */
        $option = ProductOption::factory()->create();
        /** @var Collection<ProductCategory, int> $categories */
        $categories = ProductCategory::factory()->count(5)->create();
        $option->categories()->attach($categories->pluck('id'));
        $variables = $this->getInputVariables(false, ['id' => $option->id, 'categories' => []]);

        $this->actingAs($this->createAdminUser())->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery($variables));

        $this->assertEquals(0, $option->categories()->count());

        // Detach categories which does not exist in array
        /** @var ProductOption $option */
        $option = ProductOption::factory()->create();
        /** @var Collection<ProductCategory, int> $categories */
        $categories = ProductCategory::factory()->count(5)->create();
        $option->categories()->attach($categories->pluck('id'));
        $categoryIds = $categories->random(3)->pluck('id');
        $variables = $this->getInputVariables(false, ['id' => $option->id, 'categories' => $categoryIds]);

        $this->actingAs($this->createAdminUser())->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery($variables));

        $this->assertEquals(3, $option->categories()->whereIn('ecommerce_product_categories.id', $categoryIds)->count());
    }

    #[Test]
    public function errorValidation(): void
    {
        // GraphQL type errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery([
                'name_uz' => null,
                'name_ru' => null,
                'name_en' => null,
                'name_oz' => null,
                'type' => null,
                'description' => null,
            ]));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$name_uz" of non-null type "String!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$name_ru" of non-null type "String!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$name_en" of non-null type "String!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$name_oz" of non-null type "String!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$type" of non-null type "String!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$description" of non-null type "String!" must not be null.'));

        // Rule errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery([
                'id' => 999999999,
                'name_uz' => Str::random(256),
                'name_ru' => Str::random(256),
                'name_en' => Str::random(256),
                'name_oz' => Str::random(256),
                'type' => 'abc',
                'description' => Str::random(256),
                'categories' => [999999999],
            ]));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'id']), $response->json('errors.0.extensions.validation.id.0'));
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'product option.name uz', 'max' => 255]), $response->json('errors.0.extensions.validation')['productOption.name_uz'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'product option.name ru', 'max' => 255]), $response->json('errors.0.extensions.validation')['productOption.name_ru'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'product option.name en', 'max' => 255]), $response->json('errors.0.extensions.validation')['productOption.name_en'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'product option.name oz', 'max' => 255]), $response->json('errors.0.extensions.validation')['productOption.name_oz'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'product option.description', 'max' => 255]), $response->json('errors.0.extensions.validation')['productOption.description'][0]);
        $this->assertEquals(trans('validation.enum', ['attribute' => 'product option.type']), $response->json('errors.0.extensions.validation')['productOption.type'][0]);
        $this->assertEquals(trans('validation.exists', ['attribute' => 'productOption.categories.0']), $response->json('errors.0.extensions.validation')['productOption.categories.0'][0]);
    }

    #[Test]
    public function errorUniqueSlugValidation()
    {
        /** @var ProductOption $existOption */
        $existOption = ProductOption::factory()->make(['name_en' => 'thi$ !s n@me en']);
        $existOption->slug = $existOption->name_en;
        $existOption->saveOrFail();
        /** @var ProductOption $option */
        $option = ProductOption::factory()->create();
        $variables = $this->getInputVariables(true, ['id' => $option->id, 'name_en' => $existOption->name_en]);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery($variables));

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.unique', ['attribute' => 'name']), $response->json('errors.0.extensions.validation')['productOption.name_en'][0]);
    }

    #[Test]
    public function errorUpdateProductOptionWithInactiveCompany(): void
    {
        /** @var ProductOption $option */
        $option = ProductOption::factory()->create();
        $variables = $this->getInputVariables(false, ['id' => $option->id]);

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
                mutation UpdateProductOption(
                    \$id: Int!
                    \$name_uz: String!
                    \$name_ru: String!
                    \$name_en: String!
                    \$name_oz: String!
                    \$type: String!
                    \$description: String!
                    \$categories: [Int]
                ) {
                    updateProductOption(
                        id: \$id
                        productOption: {
                            name_uz: \$name_uz
                            name_ru: \$name_ru
                            name_en: \$name_en
                            name_oz: \$name_oz
                            type: \$type
                            description: \$description
                            categories: \$categories
                        }
                    ) {
                        {$this->convertIntoQuery($this->_structure)}
                    }
                }
            GRAPHQL,
            'variables' => $variables,
        ];
    }

    private function assertSuccessUpdateProductOptionResponse(TestResponse $response, ProductOption $option, array $variables, bool $onlyRequired = false): void
    {
        $response->assertOk();
        $response->assertJsonStructure(['data' => ['updateProductOption']]);

        $option->refresh();

        $this->assertEquals($option->name_uz, $variables['name_uz']);
        $this->assertEquals($option->name_ru, $variables['name_ru']);
        $this->assertEquals($option->name_en, $variables['name_en']);
        $this->assertEquals($option->name_oz, $variables['name_oz']);
        $this->assertEquals($option->slug, Str::slug($variables['name_en']));
        $this->assertEquals($option->type->value, $variables['type']);
        $this->assertEquals($option->description, $variables['description']);
        if (! $onlyRequired) {
            $this->assertEquals($option->categories->count(), count($variables['categories']));
            $this->assertEquals($option->categories()->whereIn('ecommerce_product_categories.id', $variables['categories'])->count(), count($variables['categories']));
        } else {
            $this->assertEquals($option->categories->count(), 0);
        }

        $this->assertEquals($option->name_uz, $response->json('data.updateProductOption.name_uz'));
        $this->assertEquals($option->name_ru, $response->json('data.updateProductOption.name_ru'));
        $this->assertEquals($option->name_en, $response->json('data.updateProductOption.name_en'));
        $this->assertEquals($option->name_oz, $response->json('data.updateProductOption.name_oz'));
        $this->assertEquals($option->slug, $response->json('data.updateProductOption.slug'));
        $this->assertEquals($option->type->value, $response->json('data.updateProductOption.type'));
        $this->assertEquals($option->description, $response->json('data.updateProductOption.description'));
    }

    private function getInputVariables(bool $isRequired = false, array $variables = [])
    {
        return array_merge(
            [
                'name_uz' => Str::random(255),
                'name_ru' => Str::random(255),
                'name_en' => Str::random(255),
                'name_oz' => Str::random(255),
                'type' => OptionTypeEnum::randomValue(),
                'description' => Str::random(255),
                'categories' => ! $isRequired ? [
                    ProductCategory::factory()->create()->id,
                    ProductCategory::factory()->create()->id,
                ] : null,
            ],
            $variables
        );
    }
}
