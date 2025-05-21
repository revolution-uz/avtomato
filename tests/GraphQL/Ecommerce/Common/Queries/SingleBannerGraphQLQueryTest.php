<?php

namespace Tests\GraphQL\Ecommerce\Common\Queries;

use App\Models\Company\Ecommerce\Banner;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class SingleBannerGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private array $_structure = [
        'id',
        'name',
        'name_uz',
        'name_ru',
        'name_en',
        'name_oz',
        'is_active',
        'url',
        'order',
        'starts_at',
        'ends_at',
        'category_id',
        'brand_id',
        'product_id',
        'image_uz_id',
        'image_ru_id',
        'image_oz_id',
        'image_en_id',
        'created_at',
        'updated_at',
        'deleted_at',
        'image_uz' => [
            'id',
            'url',
            'original_name',
            'mime',
            'order',
            'created_at',
            'updated_at',
            'deleted_at',
        ],
        'image_ru' => [
            'id',
            'url',
            'original_name',
            'mime',
            'order',
            'created_at',
            'updated_at',
            'deleted_at',
        ],
        'image_en' => [
            'id',
            'url',
            'original_name',
            'mime',
            'order',
            'created_at',
            'updated_at',
            'deleted_at',
        ],
        'image_oz' => [
            'id',
            'url',
            'original_name',
            'mime',
            'order',
            'created_at',
            'updated_at',
            'deleted_at',
        ],
        'category' => [
            'id',
            'name',
            'name_uz',
            'name_ru',
            'name_en',
            'name_oz',
            'is_active',
            'parent_id',
            'image_id',
            'created_at',
            'updated_at',
            'deleted_at',
        ],
        'brand' => [
            'id',
            'name',
            'name_uz',
            'name_ru',
            'name_en',
            'name_oz',
            'is_active',
            'created_at',
            'updated_at',
            'deleted_at',
        ],
        'product' => [
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
        ],
    ];

    #[Test]
    public function successSingleBanner()
    {
        /** @var Banner $banner */
        $banner = Banner::factory()->create();

        $response = $this->postJson(
            route('graphql.ecommerce-common'), $this->buildGraphQLQuery(['id' => $banner->id])
        );

        $this->assertSuccessSingleBannerResponse($response, $banner);
    }

    #[Test]
    public function errorValidation()
    {
        // GraphQL type error
        $response = $this->postJson(
            route('graphql.ecommerce-common'), $this->buildGraphQLQuery(['id' => null])
        );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('Variable "$id" of non-null type "Int!" must not be null.', $response->json('errors.0.message'));

        // Rule errors
        $response = $this->postJson(
            route('graphql.ecommerce-common'), $this->buildGraphQLQuery(['id' => 999999999])
        );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'id']), $response->json('errors.0.extensions.validation.id.0'));
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        return [
            'query' => <<<GRAPHQL
                query SingleBanner(\$id: Int!) {
                    singleBanner(id: \$id) {
                        {$this->convertIntoQuery($this->_structure)}
                    }
                }
            GRAPHQL,
            'variables' => $variables,
        ];
    }

    private function assertSuccessSingleBannerResponse(TestResponse $response, Banner $banner): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure(['data' => ['singleBanner' => $this->_structure]]);

        $this->assertEquals($banner->id, $response->json('data.singleBanner.id'));
        $this->assertEquals($banner->name, $response->json('data.singleBanner.name'));
        $this->assertEquals($banner->name_uz, $response->json('data.singleBanner.name_uz'));
        $this->assertEquals($banner->name_ru, $response->json('data.singleBanner.name_ru'));
        $this->assertEquals($banner->name_en, $response->json('data.singleBanner.name_en'));
        $this->assertEquals($banner->name_oz, $response->json('data.singleBanner.name_oz'));
        $this->assertEquals($banner->is_active, $response->json('data.singleBanner.is_active'));
        $this->assertEquals($banner->url, $response->json('data.singleBanner.url'));
        $this->assertEquals($banner->order, $response->json('data.singleBanner.order'));
        $this->assertEquals($banner->starts_at->timestamp, $response->json('data.singleBanner.starts_at'));
        $this->assertEquals($banner->ends_at->timestamp, $response->json('data.singleBanner.ends_at'));
        $this->assertEquals($banner->category_id, $response->json('data.singleBanner.category_id'));
        $this->assertEquals($banner->brand_id, $response->json('data.singleBanner.brand_id'));
        $this->assertEquals($banner->product_id, $response->json('data.singleBanner.product_id'));
        $this->assertEquals($banner->image_uz_id, $response->json('data.singleBanner.image_uz_id'));
        $this->assertEquals($banner->image_ru_id, $response->json('data.singleBanner.image_ru_id'));
        $this->assertEquals($banner->image_oz_id, $response->json('data.singleBanner.image_oz_id'));
        $this->assertEquals($banner->image_en_id, $response->json('data.singleBanner.image_en_id'));

        // image uz
        $this->assertEquals($banner->imageUz->id, $response->json('data.singleBanner.image_uz.id'));
        $this->assertEquals($banner->imageUz->url, $response->json('data.singleBanner.image_uz.url'));
        $this->assertEquals($banner->imageUz->original_name, $response->json('data.singleBanner.image_uz.original_name'));
        $this->assertEquals($banner->imageUz->mime, $response->json('data.singleBanner.image_uz.mime'));
        $this->assertEquals($banner->imageUz->created_at->timestamp, $response->json('data.singleBanner.image_uz.created_at'));
        $this->assertEquals($banner->imageUz->updated_at->timestamp, $response->json('data.singleBanner.image_uz.updated_at'));
        $this->assertEquals($banner->imageUz->deleted_at?->timestamp, $response->json('data.singleBanner.image_uz.deleted_at'));

        // image ru
        $this->assertEquals($banner->imageRu->id, $response->json('data.singleBanner.image_ru.id'));
        $this->assertEquals($banner->imageRu->url, $response->json('data.singleBanner.image_ru.url'));
        $this->assertEquals($banner->imageRu->original_name, $response->json('data.singleBanner.image_ru.original_name'));
        $this->assertEquals($banner->imageRu->mime, $response->json('data.singleBanner.image_ru.mime'));
        $this->assertEquals($banner->imageRu->created_at->timestamp, $response->json('data.singleBanner.image_ru.created_at'));
        $this->assertEquals($banner->imageRu->updated_at->timestamp, $response->json('data.singleBanner.image_ru.updated_at'));
        $this->assertEquals($banner->imageRu->deleted_at?->timestamp, $response->json('data.singleBanner.image_ru.deleted_at'));

        // image en
        $this->assertEquals($banner->imageEn->id, $response->json('data.singleBanner.image_en.id'));
        $this->assertEquals($banner->imageEn->url, $response->json('data.singleBanner.image_en.url'));
        $this->assertEquals($banner->imageEn->original_name, $response->json('data.singleBanner.image_en.original_name'));
        $this->assertEquals($banner->imageEn->mime, $response->json('data.singleBanner.image_en.mime'));
        $this->assertEquals($banner->imageEn->created_at->timestamp, $response->json('data.singleBanner.image_en.created_at'));
        $this->assertEquals($banner->imageEn->updated_at->timestamp, $response->json('data.singleBanner.image_en.updated_at'));
        $this->assertEquals($banner->imageEn->deleted_at?->timestamp, $response->json('data.singleBanner.image_en.deleted_at'));

        // image oz
        $this->assertEquals($banner->imageOz->id, $response->json('data.singleBanner.image_oz.id'));
        $this->assertEquals($banner->imageOz->url, $response->json('data.singleBanner.image_oz.url'));
        $this->assertEquals($banner->imageOz->original_name, $response->json('data.singleBanner.image_oz.original_name'));
        $this->assertEquals($banner->imageOz->mime, $response->json('data.singleBanner.image_oz.mime'));
        $this->assertEquals($banner->imageOz->created_at->timestamp, $response->json('data.singleBanner.image_oz.created_at'));
        $this->assertEquals($banner->imageOz->updated_at->timestamp, $response->json('data.singleBanner.image_oz.updated_at'));
        $this->assertEquals($banner->imageOz->deleted_at?->timestamp, $response->json('data.singleBanner.image_oz.deleted_at'));

        // category
        $this->assertEquals($banner->category->id, $response->json('data.singleBanner.category.id'));
        $this->assertEquals($banner->category->name, $response->json('data.singleBanner.category.name'));
        $this->assertEquals($banner->category->name_uz, $response->json('data.singleBanner.category.name_uz'));
        $this->assertEquals($banner->category->name_ru, $response->json('data.singleBanner.category.name_ru'));
        $this->assertEquals($banner->category->name_en, $response->json('data.singleBanner.category.name_en'));
        $this->assertEquals($banner->category->name_oz, $response->json('data.singleBanner.category.name_oz'));
        $this->assertEquals($banner->category->is_active, $response->json('data.singleBanner.category.is_active'));
        $this->assertEquals($banner->category->parent_id, $response->json('data.singleBanner.category.parent_id'));
        $this->assertEquals($banner->category->image_id, $response->json('data.singleBanner.category.image_id'));
        $this->assertEquals($banner->category->created_at->timestamp, $response->json('data.singleBanner.category.created_at'));
        $this->assertEquals($banner->category->updated_at->timestamp, $response->json('data.singleBanner.category.updated_at'));
        $this->assertEquals($banner->category->deleted_at?->timestamp, $response->json('data.singleBanner.category.deleted_at'));

        // brand
        $this->assertEquals($banner->brand->id, $response->json('data.singleBanner.brand.id'));
        $this->assertEquals($banner->brand->name, $response->json('data.singleBanner.brand.name'));
        $this->assertEquals($banner->brand->name_uz, $response->json('data.singleBanner.brand.name_uz'));
        $this->assertEquals($banner->brand->name_ru, $response->json('data.singleBanner.brand.name_ru'));
        $this->assertEquals($banner->brand->name_en, $response->json('data.singleBanner.brand.name_en'));
        $this->assertEquals($banner->brand->name_oz, $response->json('data.singleBanner.brand.name_oz'));
        $this->assertEquals($banner->brand->is_active, $response->json('data.singleBanner.brand.is_active'));
        $this->assertEquals($banner->brand->created_at->timestamp, $response->json('data.singleBanner.brand.created_at'));
        $this->assertEquals($banner->brand->updated_at->timestamp, $response->json('data.singleBanner.brand.updated_at'));
        $this->assertEquals($banner->brand->deleted_at?->timestamp, $response->json('data.singleBanner.brand.deleted_at'));

        // product
        $this->assertEquals($banner->product->id, $response->json('data.singleBanner.product.id'));
        $this->assertEquals($banner->product->name, $response->json('data.singleBanner.product.name'));
        $this->assertEquals($banner->product->name_uz, $response->json('data.singleBanner.product.name_uz'));
        $this->assertEquals($banner->product->name_ru, $response->json('data.singleBanner.product.name_ru'));
        $this->assertEquals($banner->product->name_en, $response->json('data.singleBanner.product.name_en'));
        $this->assertEquals($banner->product->name_oz, $response->json('data.singleBanner.product.name_oz'));
        $this->assertEquals($banner->product->slug, $response->json('data.singleBanner.product.slug'));
        $this->assertEquals($banner->product->cost, $response->json('data.singleBanner.product.cost'));
        $this->assertEquals($banner->product->actual_price, $response->json('data.singleBanner.product.actual_price'));
        $this->assertEquals($banner->product->old_price, $response->json('data.singleBanner.product.old_price'));
        $this->assertEquals($banner->product->is_active, $response->json('data.singleBanner.product.is_active'));
        $this->assertEquals($banner->product->category_id, $response->json('data.singleBanner.product.category_id'));
        $this->assertEquals($banner->product->brand_id, $response->json('data.singleBanner.product.brand_id'));
        $this->assertEquals($banner->product->created_at->timestamp, $response->json('data.singleBanner.product.created_at'));
        $this->assertEquals($banner->product->updated_at->timestamp, $response->json('data.singleBanner.product.updated_at'));
        $this->assertEquals($banner->product->deleted_at?->timestamp, $response->json('data.singleBanner.product.deleted_at'));
    }
}
