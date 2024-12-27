<?php

namespace Tests\GraphQL\Ecommerce\Admin\Queries;

use App\Models\Company\Ecommerce\ProductCategory;
use App\Models\Company\Ecommerce\ProductOption;
use App\Models\Company\Ecommerce\ProductOptionValue;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Ramsey\Collection\Collection;
use Tests\GraphQL\GraphQLTestCase;

class SingleProductOptionGraphQLQueryTest extends GraphQLTestCase
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
                'option_id',
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
                'parent' => [
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
        ],
    ];

    #[Test]
    public function successSingleProductOption()
    {
        /** @var ProductOption $option */
        $option = ProductOption::factory()->create();

        /** @var ProductOptionValue[]|Collection<ProductOptionValue> $values */
        $values = ProductOptionValue::factory()->count(5)->create(['option_id' => $option->id]);

        /** @var ProductCategory[]|Collection<ProductCategory> $categories */
        $categories = ProductCategory::factory()->withParent()->count(5)->create();

        $option->categories()->attach($categories);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['id' => $option->id]));

        $this->assertSuccessSingleProductOptionResponse($response, $option);
    }

    #[Test]
    public function errorValidation()
    {
        // GraphQL type error
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['id' => null]));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('Variable "$id" of non-null type "Int!" must not be null.', $response->json('errors.0.message'));

        // Rule errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['id' => 999999999]));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'id']), $response->json('errors.0.extensions.validation.id.0'));
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        return [
            'query' => <<<GRAPHQL
                query SingleProductOption(\$id: Int!) {
                    singleProductOption(id: \$id) {
                        {$this->convertIntoQuery($this->_structure)}
                    }
                }
            GRAPHQL,
            'variables' => $variables,
        ];
    }

    private function assertSuccessSingleProductOptionResponse(TestResponse $response, ProductOption $option): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure(['data' => ['singleProductOption' => $this->_structure]]);

        $this->assertEquals($option->id, $response->json('data.singleProductOption.id'));
        $this->assertEquals($option->name_uz, $response->json('data.singleProductOption.name_uz'));
        $this->assertEquals($option->name_ru, $response->json('data.singleProductOption.name_ru'));
        $this->assertEquals($option->name_en, $response->json('data.singleProductOption.name_en'));
        $this->assertEquals($option->name_oz, $response->json('data.singleProductOption.name_oz'));
        $this->assertEquals($option->slug, $response->json('data.singleProductOption.slug'));
        $this->assertEquals($option->type->value, $response->json('data.singleProductOption.type'));
        $this->assertEquals($option->description, $response->json('data.singleProductOption.description'));
        $this->assertEquals($option->created_at->timestamp, $response->json('data.singleProductOption.created_at'));
        $this->assertEquals($option->updated_at->timestamp, $response->json('data.singleProductOption.updated_at'));
        $this->assertEquals($option->deleted_at?->timestamp, $response->json('data.singleProductOption.deleted_at'));

        // values
        $this->assertEquals($option->values()->count(), count($response->json('data.singleProductOption.values')));
        /** @var ProductOptionValue $value */
        $value = $option->values[0];
        $this->assertEquals($value->id, $response->json('data.singleProductOption.values.0.id'));
        $this->assertEquals($value->value_uz, $response->json('data.singleProductOption.values.0.value_uz'));
        $this->assertEquals($value->value_ru, $response->json('data.singleProductOption.values.0.value_ru'));
        $this->assertEquals($value->value_en, $response->json('data.singleProductOption.values.0.value_en'));
        $this->assertEquals($value->value_oz, $response->json('data.singleProductOption.values.0.value_oz'));
        $this->assertEquals($value->slug, $response->json('data.singleProductOption.values.0.slug'));
        $this->assertEquals($value->option_id, $response->json('data.singleProductOption.values.0.option_id'));
        $this->assertEquals($value->image_id, $response->json('data.singleProductOption.values.0.image_id'));
        $this->assertEquals($value->created_at->timestamp, $response->json('data.singleProductOption.values.0.created_at'));
        $this->assertEquals($value->updated_at->timestamp, $response->json('data.singleProductOption.values.0.updated_at'));
        $this->assertEquals($value->deleted_at?->timestamp, $response->json('data.singleProductOption.values.0.deleted_at'));

        // values -> image
        $this->assertEquals($value->image->id, $response->json('data.singleProductOption.values.0.image.id'));
        $this->assertEquals($value->image->url, $response->json('data.singleProductOption.values.0.image.url'));
        $this->assertEquals($value->image->original_name, $response->json('data.singleProductOption.values.0.image.original_name'));
        $this->assertEquals($value->image->mime, $response->json('data.singleProductOption.values.0.image.mime'));
        $this->assertNull($response->json('data.singleProductOption.values.0.image.order'));
        $this->assertEquals($value->image->created_at->timestamp, $response->json('data.singleProductOption.values.0.image.created_at'));
        $this->assertEquals($value->image->updated_at->timestamp, $response->json('data.singleProductOption.values.0.image.updated_at'));
        $this->assertEquals($value->image->deleted_at?->timestamp, $response->json('data.singleProductOption.values.0.image.deleted_at'));

        // categories
        $this->assertEquals($option->categories()->count(), count($response->json('data.singleProductOption.categories')));
        /** @var ProductCategory $category */
        $category = $option->categories[0];
        $this->assertEquals($category->id, $response->json('data.singleProductOption.categories.0.id'));
        $this->assertEquals($category->parent_id, $response->json('data.singleProductOption.categories.0.parent_id'));
        $this->assertEquals($category->name_uz, $response->json('data.singleProductOption.categories.0.name_uz'));
        $this->assertEquals($category->name_ru, $response->json('data.singleProductOption.categories.0.name_ru'));
        $this->assertEquals($category->name_en, $response->json('data.singleProductOption.categories.0.name_en'));
        $this->assertEquals($category->name_oz, $response->json('data.singleProductOption.categories.0.name_oz'));
        $this->assertEquals($category->is_active, $response->json('data.singleProductOption.categories.0.is_active'));
        $this->assertEquals($category->image_id, $response->json('data.singleProductOption.categories.0.image_id'));
        $this->assertEquals($category->created_at->timestamp, $response->json('data.singleProductOption.categories.0.created_at'));
        $this->assertEquals($category->updated_at->timestamp, $response->json('data.singleProductOption.categories.0.updated_at'));
        $this->assertEquals($category->deleted_at?->timestamp, $response->json('data.singleProductOption.categories.0.deleted_at'));

        // categories -> image
        $this->assertEquals($category->image->id, $response->json('data.singleProductOption.categories.0.image.id'));
        $this->assertEquals($category->image->url, $response->json('data.singleProductOption.categories.0.image.url'));
        $this->assertEquals($category->image->original_name, $response->json('data.singleProductOption.categories.0.image.original_name'));
        $this->assertEquals($category->image->mime, $response->json('data.singleProductOption.categories.0.image.mime'));
        $this->assertNull($response->json('data.singleProductOption.categories.0.image.order'));
        $this->assertEquals($category->image->created_at->timestamp, $response->json('data.singleProductOption.categories.0.image.created_at'));
        $this->assertEquals($category->image->updated_at->timestamp, $response->json('data.singleProductOption.categories.0.image.updated_at'));
        $this->assertEquals($category->image->deleted_at?->timestamp, $response->json('data.singleProductOption.categories.0.image.deleted_at'));

        // categories -> parent
        $this->assertEquals($category->parent->id, $response->json('data.singleProductOption.categories.0.parent.id'));
        $this->assertEquals($category->parent->parent_id, $response->json('data.singleProductOption.categories.0.parent.parent_id'));
        $this->assertEquals($category->parent->name_uz, $response->json('data.singleProductOption.categories.0.parent.name_uz'));
        $this->assertEquals($category->parent->name_ru, $response->json('data.singleProductOption.categories.0.parent.name_ru'));
        $this->assertEquals($category->parent->name_en, $response->json('data.singleProductOption.categories.0.parent.name_en'));
        $this->assertEquals($category->parent->name_oz, $response->json('data.singleProductOption.categories.0.parent.name_oz'));
        $this->assertEquals($category->parent->is_active, $response->json('data.singleProductOption.categories.0.parent.is_active'));
        $this->assertEquals($category->parent->parent_id, $response->json('data.singleProductOption.categories.0.parent.parent_id'));
        $this->assertEquals($category->parent->created_at->timestamp, $response->json('data.singleProductOption.categories.0.parent.created_at'));
        $this->assertEquals($category->parent->updated_at->timestamp, $response->json('data.singleProductOption.categories.0.parent.updated_at'));
        $this->assertEquals($category->parent->deleted_at?->timestamp, $response->json('data.singleProductOption.categories.0.parent.deleted_at'));
    }
}
