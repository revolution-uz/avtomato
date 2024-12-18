<?php

namespace Tests\GraphQL\Ecommerce\Admin\Mutations;

use App\Enums\Company\CompanyStatusEnum;
use App\Models\Company\Ecommerce\ProductCategory;
use App\Models\Company\Ecommerce\ProductOption;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class CreateProductOptionGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private array $_structure = [
        'id',
        'name_uz',
        'name_ru',
        'name_en',
        'name_oz',
        'slug',
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
    public function successCreateProductOption()
    {
        $data = $this->getInputVariables();

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery($data));

        $this->assertSuccessCreateProductOptionResponse($response, $data);
    }

    #[Test]
    public function successCreateProductOptionWithOnlyRequiredAttributes()
    {
        $option = $this->getInputVariables(true);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery($option));

        $this->assertSuccessCreateProductOptionResponse($response, $option, true);
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
                'description' => null,
            ]));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$name_uz" of non-null type "String!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$name_ru" of non-null type "String!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$name_en" of non-null type "String!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$name_oz" of non-null type "String!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$description" of non-null type "String!" must not be null.'));

        // rule errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery([
                'name_uz' => Str::random(256),
                'name_ru' => Str::random(256),
                'name_en' => Str::random(256),
                'name_oz' => Str::random(256),
                'description' => Str::random(256),
                'categories' => [999999999],
            ]));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'product option.name uz', 'max' => 255]), $response->json('errors.0.extensions.validation')['productOption.name_uz'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'product option.name ru', 'max' => 255]), $response->json('errors.0.extensions.validation')['productOption.name_ru'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'product option.name en', 'max' => 255]), $response->json('errors.0.extensions.validation')['productOption.name_en'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'product option.name oz', 'max' => 255]), $response->json('errors.0.extensions.validation')['productOption.name_oz'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'product option.description', 'max' => 255]), $response->json('errors.0.extensions.validation')['productOption.description'][0]);
        $this->assertEquals(trans('validation.exists', ['attribute' => 'productOption.categories.0']), $response->json('errors.0.extensions.validation')['productOption.categories.0'][0]);
    }

    #[Test]
    public function errorUniqueSlugValidation()
    {
        /** @var ProductOption $option */
        $option = ProductOption::factory()->make(['name_en' => 'thi$ !s n@me en']);
        $option->slug = $option->name_en;
        $option->saveOrFail();

        $data = $this->getInputVariables(true, ['name_en' => $option->name_en]);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery($data));

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.unique', ['attribute' => 'name']), $response->json('errors.0.extensions.validation')['productOption.name_en'][0]);
    }

    #[Test]
    public function errorCreateProductVariationsWithInactiveCompany(): void
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
                mutation CreateProductOption(
                    \$name_uz: String!
                    \$name_ru: String!
                    \$name_en: String!
                    \$name_oz: String!
                    \$description: String!
                    \$categories: [Int]
                ) {
                    createProductOption(
                        productOption: {
                            name_uz: \$name_uz
                            name_ru: \$name_ru
                            name_en: \$name_en
                            name_oz: \$name_oz
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

    private function assertSuccessCreateProductOptionResponse(TestResponse $response, array $data, bool $onlyRequired = false): void
    {
        $response->assertOk();
        $response->assertJsonStructure(['data' => ['createProductOption']]);

        $option = ProductOption::query()->with('categories')->findOrFail($response->json('data.createProductOption.id'));

        $this->assertEquals($option->name_uz, $data['name_uz']);
        $this->assertEquals($option->name_ru, $data['name_ru']);
        $this->assertEquals($option->name_en, $data['name_en']);
        $this->assertEquals($option->name_oz, $data['name_oz']);
        $this->assertEquals($option->description, $data['description']);
        if (! $onlyRequired) {
            $this->assertEquals($option->categories->count(), count($data['categories']));
            $this->assertEquals($option->categories()->whereIn('ecommerce_product_categories.id', $data['categories'])->count(), count($data['categories']));
        } else {
            $this->assertEquals($option->categories->count(), 0);
        }

        $this->assertEquals($option->name_uz, $response->json('data.createProductOption.name_uz'));
        $this->assertEquals($option->name_ru, $response->json('data.createProductOption.name_ru'));
        $this->assertEquals($option->name_en, $response->json('data.createProductOption.name_en'));
        $this->assertEquals($option->name_oz, $response->json('data.createProductOption.name_oz'));
        $this->assertEquals($option->slug, $response->json('data.createProductOption.slug'));
        $this->assertEquals($option->description, $response->json('data.createProductOption.description'));
    }

    private function getInputVariables(bool $isRequired = false, array $variables = [])
    {
        return array_merge(
            [
                'name_uz' => Str::random(255),
                'name_ru' => Str::random(255),
                'name_en' => Str::random(255),
                'name_oz' => Str::random(255),
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
