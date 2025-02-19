<?php

namespace Tests\GraphQL\Ecommerce\Admin\Queries;

use App\Models\Company\Ecommerce\ProductOptionValue;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class SingleProductOptionValueGraphQLQueryTest extends GraphQLTestCase
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
            'type',
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
    public function successSingleProductOptionValue()
    {
        $value = ProductOptionValue::factory()->create();

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['id' => $value->id]));

        $this->assertSuccessSingleProductOptionValueResponse($response, $value);
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

        // rule errors
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
                query SingleProductOptionValue(\$id: Int!) {
                    singleProductOptionValue(id: \$id) {
                        {$this->convertIntoQuery($this->_structure)}
                    }
                }
            GRAPHQL,
            'variables' => $variables,
        ];
    }

    private function assertSuccessSingleProductOptionValueResponse(TestResponse $response, ProductOptionValue $value): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'singleProductOptionValue' => $this->_structure,
            ],
        ]);

        $this->assertNotNull($value);

        $this->assertEquals($value->id, $response->json('data.singleProductOptionValue.id'));
        $this->assertEquals($value->value_uz, $response->json('data.singleProductOptionValue.value_uz'));
        $this->assertEquals($value->value_ru, $response->json('data.singleProductOptionValue.value_ru'));
        $this->assertEquals($value->value_en, $response->json('data.singleProductOptionValue.value_en'));
        $this->assertEquals($value->value_oz, $response->json('data.singleProductOptionValue.value_oz'));
        $this->assertEquals($value->slug, $response->json('data.singleProductOptionValue.slug'));
        $this->assertEquals($value->option_id, $response->json('data.singleProductOptionValue.option_id'));
        $this->assertEquals($value->image_id, $response->json('data.singleProductOptionValue.image_id'));
        $this->assertEquals($value->created_at->timestamp, $response->json('data.singleProductOptionValue.created_at'));
        $this->assertEquals($value->updated_at->timestamp, $response->json('data.singleProductOptionValue.updated_at'));
        $this->assertEquals($value->deleted_at?->timestamp, $response->json('data.singleProductOptionValue.deleted_at'));

        // option
        $this->assertEquals($value->option->id, $response->json('data.singleProductOptionValue.option.id'));
        $this->assertEquals($value->option->name_uz, $response->json('data.singleProductOptionValue.option.name_uz'));
        $this->assertEquals($value->option->name_ru, $response->json('data.singleProductOptionValue.option.name_ru'));
        $this->assertEquals($value->option->name_en, $response->json('data.singleProductOptionValue.option.name_en'));
        $this->assertEquals($value->option->name_oz, $response->json('data.singleProductOptionValue.option.name_oz'));
        $this->assertEquals($value->option->slug, $response->json('data.singleProductOptionValue.option.slug'));
        $this->assertEquals($value->option->type->value, $response->json('data.singleProductOptionValue.option.type'));
        $this->assertEquals($value->option->description, $response->json('data.singleProductOptionValue.option.description'));
        $this->assertEquals($value->option->created_at->timestamp, $response->json('data.singleProductOptionValue.option.created_at'));
        $this->assertEquals($value->option->updated_at->timestamp, $response->json('data.singleProductOptionValue.option.updated_at'));
        $this->assertEquals($value->option->deleted_at?->timestamp, $response->json('data.singleProductOptionValue.option.deleted_at'));

        // image
        $this->assertEquals($value->image->id, $response->json('data.singleProductOptionValue.image.id'));
        $this->assertEquals($value->image->url, $response->json('data.singleProductOptionValue.image.url'));
        $this->assertEquals($value->image->original_name, $response->json('data.singleProductOptionValue.image.original_name'));
        $this->assertEquals($value->image->mime, $response->json('data.singleProductOptionValue.image.mime'));
        $this->assertNull($response->json('data.singleProductOptionValue.image.order'));
        $this->assertEquals($value->image->created_at->timestamp, $response->json('data.singleProductOptionValue.image.created_at'));
        $this->assertEquals($value->image->updated_at->timestamp, $response->json('data.singleProductOptionValue.image.updated_at'));
        $this->assertEquals($value->image->deleted_at?->timestamp, $response->json('data.singleProductOptionValue.image.deleted_at'));
    }
}
