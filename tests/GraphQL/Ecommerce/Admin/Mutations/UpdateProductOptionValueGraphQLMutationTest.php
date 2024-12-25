<?php

namespace Tests\GraphQL\Ecommerce\Admin\Mutations;

use App\Enums\Company\CompanyStatusEnum;
use App\Models\Company\Ecommerce\EcommerceImage;
use App\Models\Company\Ecommerce\ProductOption;
use App\Models\Company\Ecommerce\ProductOptionValue;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class UpdateProductOptionValueGraphQLMutationTest extends GraphQLTestCase
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
        'image_id',
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
    public function successUpdateProductOptionValue()
    {
        /** @var ProductOptionValue $value */
        $value = ProductOptionValue::factory()->make();
        $value->slug = $value->value_en;
        $value->saveOrFail();
        $variables = $this->getInputVariables(false, ['id' => $value->id, 'value_en' => $value->value_en]);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery($variables));

        $this->assertSuccessUpdateProductOptionValueResponse($response, $value, $variables);
    }

    #[Test]
    public function successUpdateProductOptionValueWithOnlyRequiredAttributes()
    {
        /** @var ProductOptionValue $value */
        $value = ProductOptionValue::factory()->create();
        $variables = $this->getInputVariables(true, ['id' => $value->id]);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery($variables));

        $this->assertSuccessUpdateProductOptionValueResponse($response, $value, $variables, true);
    }

    #[Test]
    public function errorValidation(): void
    {
        // GraphQL type errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery([
                'id' => null,
                'value_uz' => null,
                'value_ru' => null,
                'value_en' => null,
                'value_oz' => null,
                'option_id' => null,
            ]));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$id" of non-null type "Int!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$value_uz" of non-null type "String!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$value_ru" of non-null type "String!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$value_en" of non-null type "String!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$value_oz" of non-null type "String!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$option_id" of non-null type "Int!" must not be null.'));

        // Rule errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery([
                'id' => 999999999,
                'value_uz' => Str::random(256),
                'value_ru' => Str::random(256),
                'value_en' => Str::random(256),
                'value_oz' => Str::random(256),
                'option_id' => 999999999,
                'image_id' => 999999999,
            ]));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'id']), $response->json('errors.0.extensions.validation.id.0'));
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'product option value.value uz', 'max' => 255]), $response->json('errors.0.extensions.validation')['productOptionValue.value_uz'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'product option value.value ru', 'max' => 255]), $response->json('errors.0.extensions.validation')['productOptionValue.value_ru'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'product option value.value en', 'max' => 255]), $response->json('errors.0.extensions.validation')['productOptionValue.value_en'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'product option value.value oz', 'max' => 255]), $response->json('errors.0.extensions.validation')['productOptionValue.value_oz'][0]);
        $this->assertEquals(trans('validation.exists', ['attribute' => 'product option value.option id']), $response->json('errors.0.extensions.validation')['productOptionValue.option_id'][0]);
        $this->assertEquals(trans('validation.exists', ['attribute' => 'product option value.image id']), $response->json('errors.0.extensions.validation')['productOptionValue.image_id'][0]);
    }

    #[Test]
    public function errorUniqueSlugValidation()
    {
        /** @var ProductOptionValue $existValue */
        $existValue = ProductOptionValue::factory()->make(['value_en' => 'thi$ !s v@lu3 en']);
        $existValue->slug = $existValue->value_en;
        $existValue->saveOrFail();
        /** @var ProductOptionValue $value */
        $value = ProductOptionValue::factory()->create();
        $variables = $this->getInputVariables(true, ['id' => $value->id, 'value_en' => $existValue->value_en]);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery($variables));

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.unique', ['attribute' => 'value']), $response->json('errors.0.extensions.validation')['productOptionValue.value_en'][0]);
    }

    #[Test]
    public function errorUpdateProductOptionValueWithInactiveCompany(): void
    {
        /** @var ProductOptionValue $value */
        $value = ProductOptionValue::factory()->create();
        $variables = $this->getInputVariables(true, ['id' => $value->id]);

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
                mutation UpdateProductOptionValue(
                    \$id: Int!
                    \$value_uz: String!
                    \$value_ru: String!
                    \$value_en: String!
                    \$value_oz: String!
                    \$option_id: Int!
                    \$image_id: Int
                ) {
                    updateProductOptionValue(
                        id: \$id
                        productOptionValue: {
                            value_uz: \$value_uz
                            value_ru: \$value_ru
                            value_en: \$value_en
                            value_oz: \$value_oz
                            option_id: \$option_id
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

    private function assertSuccessUpdateProductOptionValueResponse(TestResponse $response, ProductOptionValue $value, array $variables, bool $onlyRequired = false): void
    {
        $response->assertOk();
        ! $onlyRequired ?
            $response->assertJsonStructure(['data' => ['updateProductOptionValue' => $this->_structure]]) :
            $response->assertJsonStructure(['data' => ['updateProductOptionValue' => Arr::except($this->_structure, 'image')]]);

        $value->refresh();

        $this->assertEquals($value->value_uz, $variables['value_uz']);
        $this->assertEquals($value->value_ru, $variables['value_ru']);
        $this->assertEquals($value->value_en, $variables['value_en']);
        $this->assertEquals($value->value_oz, $variables['value_oz']);
        $this->assertEquals($value->option_id, $variables['option_id']);
        ! $onlyRequired ?
            $this->assertEquals($value->image_id, $variables['image_id']) :
            $this->assertNull($value->image_id);

        $this->assertEquals($value->value_uz, $response->json('data.updateProductOptionValue.value_uz'));
        $this->assertEquals($value->value_ru, $response->json('data.updateProductOptionValue.value_ru'));
        $this->assertEquals($value->value_en, $response->json('data.updateProductOptionValue.value_en'));
        $this->assertEquals($value->value_oz, $response->json('data.updateProductOptionValue.value_oz'));
        $this->assertEquals($value->slug, $response->json('data.updateProductOptionValue.slug'));
        $this->assertEquals($value->option_id, $response->json('data.updateProductOptionValue.option_id'));
        $this->assertEquals($value->image_id, $response->json('data.updateProductOptionValue.image_id'));
    }

    private function getInputVariables(bool $isRequired = false, array $variables = [])
    {
        return array_merge(
            [
                'value_uz' => Str::random(255),
                'value_ru' => Str::random(255),
                'value_en' => Str::random(255),
                'value_oz' => Str::random(255),
                'option_id' => ProductOption::factory()->create()->id,
                'image_id' => ! $isRequired ? EcommerceImage::factory()->create()->id : null,
            ],
            $variables
        );
    }
}
