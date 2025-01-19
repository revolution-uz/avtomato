<?php

namespace Tests\GraphQL\Ecommerce\Admin\Queries;

use App\Models\Company\Ecommerce\ProductBrand;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class SingleProductBrandGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private array $_structure = [
        'id',
        'name_uz',
        'name_ru',
        'name_en',
        'name_oz',
        'slug',
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
    ];

    #[Test]
    public function successSingleProductBrand()
    {
        /** @var ProductBrand $brand */
        $brand = ProductBrand::factory()->create();

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['id' => $brand->id]));

        $this->assertSuccessSingleProductBrandResponse($response, $brand);
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
                query SingleProductBrand(\$id: Int!) {
                    singleProductBrand(id: \$id) {
                        {$this->convertIntoQuery($this->_structure)}
                    }
                }
            GRAPHQL,
            'variables' => $variables,
        ];
    }

    private function assertSuccessSingleProductBrandResponse(TestResponse $response, ProductBrand $brand): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'singleProductBrand' => $this->_structure,
            ],
        ]);

        $this->assertEquals($brand->id, $response->json('data.singleProductBrand.id'));
        $this->assertEquals($brand->name_uz, $response->json('data.singleProductBrand.name_uz'));
        $this->assertEquals($brand->name_ru, $response->json('data.singleProductBrand.name_ru'));
        $this->assertEquals($brand->name_en, $response->json('data.singleProductBrand.name_en'));
        $this->assertEquals($brand->name_oz, $response->json('data.singleProductBrand.name_oz'));
        $this->assertEquals($brand->slug, $response->json('data.singleProductBrand.slug'));
        $this->assertEquals($brand->is_active, $response->json('data.singleProductBrand.is_active'));
        $this->assertEquals($brand->image_id, $response->json('data.singleProductBrand.image_id'));
        $this->assertEquals($brand->created_at->timestamp, $response->json('data.singleProductBrand.created_at'));
        $this->assertEquals($brand->updated_at->timestamp, $response->json('data.singleProductBrand.updated_at'));
        $this->assertEquals($brand->deleted_at?->timestamp, $response->json('data.singleProductBrand.deleted_at'));

        // image
        $this->assertEquals($brand->image->id, $response->json('data.singleProductBrand.image.id'));
        $this->assertEquals($brand->image->url, $response->json('data.singleProductBrand.image.url'));
        $this->assertEquals($brand->image->original_name, $response->json('data.singleProductBrand.image.original_name'));
        $this->assertEquals($brand->image->mime, $response->json('data.singleProductBrand.image.mime'));
        $this->assertNull($response->json('data.singleProductBrand.image.order'));
        $this->assertEquals($brand->image->created_at->timestamp, $response->json('data.singleProductBrand.image.created_at'));
        $this->assertEquals($brand->image->updated_at->timestamp, $response->json('data.singleProductBrand.image.updated_at'));
        $this->assertEquals($brand->image->deleted_at?->timestamp, $response->json('data.singleProductBrand.image.deleted_at'));
    }
}
