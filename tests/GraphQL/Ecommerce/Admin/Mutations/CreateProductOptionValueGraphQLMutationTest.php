<?php

namespace Tests\GraphQL\Ecommerce\Admin\Mutations;

use App\Enums\Company\CompanyStatusEnum;
use App\Models\Company\Ecommerce\ProductOption;
use App\Models\Company\Ecommerce\ProductOptionValue;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class CreateProductOptionValueGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private array $_structure = [
        'id',
        'value_uz',
        'value_ru',
        'value_en',
        'value_oz',
        'slug',
        'option_id',
        'created_at',
        'updated_at',
        'deleted_at',
        'option' => [
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
        ],
    ];

    #[Test]
    public function successCreateProductOptionValue()
    {
        $variables = $this->getInputVariables();

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery($variables));

        $this->assertSuccessCreateProductOptionValueResponse($response, $variables);
    }

    #[Test]
    public function errorValidation(): void
    {
        // GraphQL type errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery([
                'value_uz' => null,
                'value_ru' => null,
                'value_en' => null,
                'value_oz' => null,
                'option_id' => null,
            ]));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$value_uz" of non-null type "String!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$value_ru" of non-null type "String!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$value_en" of non-null type "String!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$value_oz" of non-null type "String!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$option_id" of non-null type "Int!" must not be null.'));

        // rule errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery([
                'value_uz' => Str::random(256),
                'value_ru' => Str::random(256),
                'value_en' => Str::random(256),
                'value_oz' => Str::random(256),
                'option_id' => 999999999,
            ]));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'product option value.value uz', 'max' => 255]), $response->json('errors.0.extensions.validation')['productOptionValue.value_uz'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'product option value.value ru', 'max' => 255]), $response->json('errors.0.extensions.validation')['productOptionValue.value_ru'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'product option value.value en', 'max' => 255]), $response->json('errors.0.extensions.validation')['productOptionValue.value_en'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'product option value.value oz', 'max' => 255]), $response->json('errors.0.extensions.validation')['productOptionValue.value_oz'][0]);
        $this->assertEquals(trans('validation.exists', ['attribute' => 'product option value.option id']), $response->json('errors.0.extensions.validation')['productOptionValue.option_id'][0]);
    }

    #[Test]
    public function errorUniqueSlugValidation()
    {
        /** @var ProductOptionValue $value */
        $value = ProductOptionValue::factory()->make(['value_en' => 'thi$ !s n@me en']);
        $value->slug = $value->value_en;
        $value->saveOrFail();

        $variables = $this->getInputVariables(['value_en' => $value->value_en]);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery($variables));

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.unique', ['attribute' => 'value']), $response->json('errors.0.extensions.validation')['productOptionValue.value_en'][0]);
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
                mutation CreateProductOptionValue(
                    \$value_uz: String!
                    \$value_ru: String!
                    \$value_en: String!
                    \$value_oz: String!
                    \$option_id: Int!
                ) {
                    createProductOptionValue(
                        productOptionValue: {
                            value_uz: \$value_uz
                            value_ru: \$value_ru
                            value_en: \$value_en
                            value_oz: \$value_oz
                            option_id: \$option_id
                        }
                    ) {
                       {$this->convertIntoQuery($this->_structure)}
                    }
                }
            GRAPHQL,
            'variables' => $variables,
        ];
    }

    private function assertSuccessCreateProductOptionValueResponse(TestResponse $response, array $variables): void
    {
        $response->assertOk();
        $response->assertJsonStructure(['data' => ['createProductOptionValue' => $this->_structure]]);

        $option = ProductOptionValue::query()->with('option')->findOrFail($response->json('data.createProductOptionValue.id'));

        $this->assertEquals($option->value_uz, $variables['value_uz']);
        $this->assertEquals($option->value_ru, $variables['value_ru']);
        $this->assertEquals($option->value_en, $variables['value_en']);
        $this->assertEquals($option->value_oz, $variables['value_oz']);
        $this->assertEquals($option->option_id, $variables['option_id']);

        $this->assertEquals($option->value_uz, $response->json('data.createProductOptionValue.value_uz'));
        $this->assertEquals($option->value_ru, $response->json('data.createProductOptionValue.value_ru'));
        $this->assertEquals($option->value_en, $response->json('data.createProductOptionValue.value_en'));
        $this->assertEquals($option->value_oz, $response->json('data.createProductOptionValue.value_oz'));
        $this->assertEquals($option->slug, $response->json('data.createProductOptionValue.slug'));
        $this->assertEquals($option->option_id, $response->json('data.createProductOptionValue.option_id'));
    }

    private function getInputVariables(array $variables = [])
    {
        return array_merge(
            [
                'value_uz' => Str::random(255),
                'value_ru' => Str::random(255),
                'value_en' => Str::random(255),
                'value_oz' => Str::random(255),
                'option_id' => ProductOption::factory()->create()->id,
            ],
            $variables
        );
    }
}
