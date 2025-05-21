<?php

namespace Tests\GraphQL\Ecommerce\Common\Queries;

use App\Models\Company\Ecommerce\Banner;
use App\Models\Company\Ecommerce\Product;
use App\Models\Company\Ecommerce\ProductCategory;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class SingleProductCategoryGraphQLQueryTest extends GraphQLTestCase
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
        'parent_id',
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
            'name',
            'name_uz',
            'name_ru',
            'name_en',
            'name_oz',
            'is_active',
            'parent_id',
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
        ],
        'products' => [
            '*' => [
                'id',
                'name',
                'name_uz',
                'name_ru',
                'name_en',
                'name_oz',
                'slug',
                'is_active',
                'cost',
                'actual_price',
                'old_price',
                'category_id',
                'brand_id',
                'created_at',
                'updated_at',
                'deleted_at',
                // 'main_image' => [
                //     'id',
                //     'url',
                //     'original_name',
                //     'mime',
                //     'created_at',
                //     'updated_at',
                //     'deleted_at',
                // ],
                // 'images' => [
                //     'id',
                //     'url',
                //     'original_name',
                //     'mime',
                //     'created_at',
                //     'updated_at',
                //     'deleted_at',
                // ],
                'variations' => [
                    '*' => [
                        'id',
                        'product_id',
                        'is_active',
                        'created_at',
                        'updated_at',
                        'deleted_at',
                    ],
                ],
            ],
        ],
        'banners' => [
            '*' => [
                'id',
                'name',
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
                'image_uz' => [
                    'id',
                    'url',
                    'original_name',
                    'mime',
                    'created_at',
                    'updated_at',
                    'deleted_at',
                ],
                'image_ru' => [
                    'id',
                    'url',
                    'original_name',
                    'mime',
                    'created_at',
                    'updated_at',
                    'deleted_at',
                ],
                'image_en' => [
                    'id',
                    'url',
                    'original_name',
                    'mime',
                    'created_at',
                    'updated_at',
                    'deleted_at',
                ],
                'image_oz' => [
                    'id',
                    'url',
                    'original_name',
                    'mime',
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
                    'is_active',
                    'cost',
                    'actual_price',
                    'old_price',
                    'category_id',
                    'brand_id',
                    'created_at',
                    'updated_at',
                    'deleted_at',
                ],
            ],
        ],
    ];

    #[Test]
    public function successSingleProductCategory()
    {
        $productCategory = ProductCategory::factory()->withParent()->create();
        Product::factory()->create(['category_id' => $productCategory->id]);
        Banner::factory()->create(['category_id' => $productCategory->id]);

        $response = $this->postJson(
            route('graphql.ecommerce-common'), $this->buildGraphQLQuery(['id' => $productCategory->id])
        );

        $this->assertSuccessSingleProductCategoryResponse($response, $productCategory);
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
                query SingleProductCategory(\$id: Int!) {
                    singleProductCategory(id: \$id) {
                        {$this->convertIntoQuery($this->_structure)}
                    }
                }
            GRAPHQL,
            'variables' => $variables,
        ];
    }

    private function assertSuccessSingleProductCategoryResponse(TestResponse $response, ProductCategory $productCategory): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure(['data' => ['singleProductCategory' => $this->_structure]]);

        $this->assertEquals($productCategory->id, $response->json('data.singleProductCategory.id'));
        $this->assertEquals($productCategory->name, $response->json('data.singleProductCategory.name'));
        $this->assertEquals($productCategory->name_uz, $response->json('data.singleProductCategory.name_uz'));
        $this->assertEquals($productCategory->name_ru, $response->json('data.singleProductCategory.name_ru'));
        $this->assertEquals($productCategory->name_en, $response->json('data.singleProductCategory.name_en'));
        $this->assertEquals($productCategory->name_oz, $response->json('data.singleProductCategory.name_oz'));
        $this->assertEquals($productCategory->is_active, $response->json('data.singleProductCategory.is_active'));
        $this->assertEquals($productCategory->parent_id, $response->json('data.singleProductCategory.parent_id'));
        $this->assertEquals($productCategory->image_id, $response->json('data.singleProductCategory.image_id'));
        $this->assertEquals($productCategory->created_at->timestamp, $response->json('data.singleProductCategory.created_at'));
        $this->assertEquals($productCategory->updated_at->timestamp, $response->json('data.singleProductCategory.updated_at'));
        $this->assertEquals($productCategory->deleted_at?->timestamp, $response->json('data.singleProductCategory.deleted_at'));

        // image
        $this->assertEquals($productCategory->image->id, $response->json('data.singleProductCategory.image.id'));
        $this->assertEquals($productCategory->image->url, $response->json('data.singleProductCategory.image.url'));
        $this->assertEquals($productCategory->image->original_name, $response->json('data.singleProductCategory.image.original_name'));
        $this->assertEquals($productCategory->image->mime, $response->json('data.singleProductCategory.image.mime'));
        $this->assertEquals($productCategory->image->created_at->timestamp, $response->json('data.singleProductCategory.image.created_at'));
        $this->assertEquals($productCategory->image->updated_at->timestamp, $response->json('data.singleProductCategory.image.updated_at'));
        $this->assertEquals($productCategory->image->deleted_at?->timestamp, $response->json('data.singleProductCategory.image.deleted_at'));

        // parent
        $this->assertEquals($productCategory->parent->id, $response->json('data.singleProductCategory.parent.id'));
        $this->assertEquals($productCategory->parent->name, $response->json('data.singleProductCategory.parent.name'));
        $this->assertEquals($productCategory->parent->name_uz, $response->json('data.singleProductCategory.parent.name_uz'));
        $this->assertEquals($productCategory->parent->name_ru, $response->json('data.singleProductCategory.parent.name_ru'));
        $this->assertEquals($productCategory->parent->name_en, $response->json('data.singleProductCategory.parent.name_en'));
        $this->assertEquals($productCategory->parent->name_oz, $response->json('data.singleProductCategory.parent.name_oz'));
        $this->assertEquals($productCategory->parent->is_active, $response->json('data.singleProductCategory.parent.is_active'));
        $this->assertEquals($productCategory->parent->parent_id, $response->json('data.singleProductCategory.parent.parent_id'));
        $this->assertEquals($productCategory->parent->created_at->timestamp, $response->json('data.singleProductCategory.parent.created_at'));
        $this->assertEquals($productCategory->parent->updated_at->timestamp, $response->json('data.singleProductCategory.parent.updated_at'));
        $this->assertEquals($productCategory->parent->deleted_at?->timestamp, $response->json('data.singleProductCategory.parent.deleted_at'));

        // parent -> image
        $this->assertEquals($productCategory->parent->image->id, $response->json('data.singleProductCategory.parent.image.id'));
        $this->assertEquals($productCategory->parent->image->url, $response->json('data.singleProductCategory.parent.image.url'));
        $this->assertEquals($productCategory->parent->image->original_name, $response->json('data.singleProductCategory.parent.image.original_name'));
        $this->assertEquals($productCategory->parent->image->mime, $response->json('data.singleProductCategory.parent.image.mime'));
        $this->assertEquals($productCategory->parent->image->created_at->timestamp, $response->json('data.singleProductCategory.parent.image.created_at'));
        $this->assertEquals($productCategory->parent->image->updated_at->timestamp, $response->json('data.singleProductCategory.parent.image.updated_at'));
        $this->assertEquals($productCategory->parent->image->deleted_at?->timestamp, $response->json('data.singleProductCategory.parent.image.deleted_at'));

        // products
        $product = $productCategory->products->first();
        $this->assertEquals($product->id, $response->json('data.singleProductCategory.products.0.id'));
        $this->assertEquals($product->name, $response->json('data.singleProductCategory.products.0.name'));
        $this->assertEquals($product->name_uz, $response->json('data.singleProductCategory.products.0.name_uz'));
        $this->assertEquals($product->name_ru, $response->json('data.singleProductCategory.products.0.name_ru'));
        $this->assertEquals($product->name_en, $response->json('data.singleProductCategory.products.0.name_en'));
        $this->assertEquals($product->name_oz, $response->json('data.singleProductCategory.products.0.name_oz'));
        $this->assertEquals($product->slug, $response->json('data.singleProductCategory.products.0.slug'));
        $this->assertEquals($product->is_active, $response->json('data.singleProductCategory.products.0.is_active'));
        $this->assertEquals($product->cost, $response->json('data.singleProductCategory.products.0.cost'));
        $this->assertEquals($product->actual_price, $response->json('data.singleProductCategory.products.0.actual_price'));
        $this->assertEquals($product->old_price, $response->json('data.singleProductCategory.products.0.old_price'));
        $this->assertEquals($product->category_id, $response->json('data.singleProductCategory.products.0.category_id'));
        $this->assertEquals($product->brand_id, $response->json('data.singleProductCategory.products.0.brand_id'));
        $this->assertEquals($product->created_at->timestamp, $response->json('data.singleProductCategory.products.0.created_at'));
        $this->assertEquals($product->updated_at->timestamp, $response->json('data.singleProductCategory.products.0.updated_at'));
        $this->assertEquals($product->deleted_at?->timestamp, $response->json('data.singleProductCategory.products.0.deleted_at'));

        // banners
        $banner = $productCategory->banners->first();
        $this->assertEquals($banner->id, $response->json('data.singleProductCategory.banners.0.id'));
        $this->assertEquals($banner->name, $response->json('data.singleProductCategory.banners.0.name'));
        $this->assertEquals($banner->name_uz, $response->json('data.singleProductCategory.banners.0.name_uz'));
        $this->assertEquals($banner->name_ru, $response->json('data.singleProductCategory.banners.0.name_ru'));
        $this->assertEquals($banner->name_en, $response->json('data.singleProductCategory.banners.0.name_en'));
        $this->assertEquals($banner->name_oz, $response->json('data.singleProductCategory.banners.0.name_oz'));
        $this->assertEquals($banner->image_uz_id, $response->json('data.singleProductCategory.banners.0.image_uz_id'));
        $this->assertEquals($banner->image_ru_id, $response->json('data.singleProductCategory.banners.0.image_ru_id'));
        $this->assertEquals($banner->image_en_id, $response->json('data.singleProductCategory.banners.0.image_en_id'));
        $this->assertEquals($banner->image_oz_id, $response->json('data.singleProductCategory.banners.0.image_oz_id'));
        $this->assertEquals($banner->is_active, $response->json('data.singleProductCategory.banners.0.is_active'));
        $this->assertEquals($banner->url, $response->json('data.singleProductCategory.banners.0.url'));
        $this->assertEquals($banner->category_id, $response->json('data.singleProductCategory.banners.0.category_id'));
        $this->assertEquals($banner->brand_id, $response->json('data.singleProductCategory.banners.0.brand_id'));
        $this->assertEquals($banner->product_id, $response->json('data.singleProductCategory.banners.0.product_id'));
        $this->assertEquals($banner->created_at->timestamp, $response->json('data.singleProductCategory.banners.0.created_at'));
        $this->assertEquals($banner->updated_at->timestamp, $response->json('data.singleProductCategory.banners.0.updated_at'));
        $this->assertEquals($banner->deleted_at?->timestamp, $response->json('data.singleProductCategory.banners.0.deleted_at'));

        // banner -> images
        $this->assertEquals($banner->imageUz->id, $response->json('data.singleProductCategory.banners.0.image_uz.id'));
        $this->assertEquals($banner->imageUz->url, $response->json('data.singleProductCategory.banners.0.image_uz.url'));
        $this->assertEquals($banner->imageUz->original_name, $response->json('data.singleProductCategory.banners.0.image_uz.original_name'));
        $this->assertEquals($banner->imageUz->mime, $response->json('data.singleProductCategory.banners.0.image_uz.mime'));
        $this->assertEquals($banner->imageUz->created_at->timestamp, $response->json('data.singleProductCategory.banners.0.image_uz.created_at'));
        $this->assertEquals($banner->imageUz->updated_at->timestamp, $response->json('data.singleProductCategory.banners.0.image_uz.updated_at'));
        $this->assertEquals($banner->imageUz->deleted_at?->timestamp, $response->json('data.singleProductCategory.banners.0.image_uz.deleted_at'));

        $this->assertEquals($banner->imageRu->id, $response->json('data.singleProductCategory.banners.0.image_ru.id'));
        $this->assertEquals($banner->imageRu->url, $response->json('data.singleProductCategory.banners.0.image_ru.url'));
        $this->assertEquals($banner->imageRu->original_name, $response->json('data.singleProductCategory.banners.0.image_ru.original_name'));
        $this->assertEquals($banner->imageRu->mime, $response->json('data.singleProductCategory.banners.0.image_ru.mime'));
        $this->assertEquals($banner->imageRu->created_at->timestamp, $response->json('data.singleProductCategory.banners.0.image_ru.created_at'));
        $this->assertEquals($banner->imageRu->updated_at->timestamp, $response->json('data.singleProductCategory.banners.0.image_ru.updated_at'));
        $this->assertEquals($banner->imageRu->deleted_at?->timestamp, $response->json('data.singleProductCategory.banners.0.image_ru.deleted_at'));

        $this->assertEquals($banner->imageEn->id, $response->json('data.singleProductCategory.banners.0.image_en.id'));
        $this->assertEquals($banner->imageEn->url, $response->json('data.singleProductCategory.banners.0.image_en.url'));
        $this->assertEquals($banner->imageEn->original_name, $response->json('data.singleProductCategory.banners.0.image_en.original_name'));
        $this->assertEquals($banner->imageEn->mime, $response->json('data.singleProductCategory.banners.0.image_en.mime'));
        $this->assertEquals($banner->imageEn->created_at->timestamp, $response->json('data.singleProductCategory.banners.0.image_en.created_at'));
        $this->assertEquals($banner->imageEn->updated_at->timestamp, $response->json('data.singleProductCategory.banners.0.image_en.updated_at'));
        $this->assertEquals($banner->imageEn->deleted_at?->timestamp, $response->json('data.singleProductCategory.banners.0.image_en.deleted_at'));

        $this->assertEquals($banner->imageOz->id, $response->json('data.singleProductCategory.banners.0.image_oz.id'));
        $this->assertEquals($banner->imageOz->url, $response->json('data.singleProductCategory.banners.0.image_oz.url'));
        $this->assertEquals($banner->imageOz->original_name, $response->json('data.singleProductCategory.banners.0.image_oz.original_name'));
        $this->assertEquals($banner->imageOz->mime, $response->json('data.singleProductCategory.banners.0.image_oz.mime'));
        $this->assertEquals($banner->imageOz->created_at->timestamp, $response->json('data.singleProductCategory.banners.0.image_oz.created_at'));
        $this->assertEquals($banner->imageOz->updated_at->timestamp, $response->json('data.singleProductCategory.banners.0.image_oz.updated_at'));
        $this->assertEquals($banner->imageOz->deleted_at?->timestamp, $response->json('data.singleProductCategory.banners.0.image_oz.deleted_at'));

        // banner -> brand
        $this->assertEquals($banner->brand->id, $response->json('data.singleProductCategory.banners.0.brand.id'));
        $this->assertEquals($banner->brand->name, $response->json('data.singleProductCategory.banners.0.brand.name'));
        $this->assertEquals($banner->brand->name_uz, $response->json('data.singleProductCategory.banners.0.brand.name_uz'));
        $this->assertEquals($banner->brand->name_ru, $response->json('data.singleProductCategory.banners.0.brand.name_ru'));
        $this->assertEquals($banner->brand->name_en, $response->json('data.singleProductCategory.banners.0.brand.name_en'));
        $this->assertEquals($banner->brand->name_oz, $response->json('data.singleProductCategory.banners.0.brand.name_oz'));
        $this->assertEquals($banner->brand->is_active, $response->json('data.singleProductCategory.banners.0.brand.is_active'));
        $this->assertEquals($banner->brand->created_at->timestamp, $response->json('data.singleProductCategory.banners.0.brand.created_at'));
        $this->assertEquals($banner->brand->updated_at->timestamp, $response->json('data.singleProductCategory.banners.0.brand.updated_at'));
        $this->assertEquals($banner->brand->deleted_at?->timestamp, $response->json('data.singleProductCategory.banners.0.brand.deleted_at'));

        // banner -> product
        $this->assertEquals($banner->product->id, $response->json('data.singleProductCategory.banners.0.product.id'));
        $this->assertEquals($banner->product->name, $response->json('data.singleProductCategory.banners.0.product.name'));
        $this->assertEquals($banner->product->name_uz, $response->json('data.singleProductCategory.banners.0.product.name_uz'));
        $this->assertEquals($banner->product->name_ru, $response->json('data.singleProductCategory.banners.0.product.name_ru'));
        $this->assertEquals($banner->product->name_en, $response->json('data.singleProductCategory.banners.0.product.name_en'));
        $this->assertEquals($banner->product->name_oz, $response->json('data.singleProductCategory.banners.0.product.name_oz'));
        $this->assertEquals($banner->product->slug, $response->json('data.singleProductCategory.banners.0.product.slug'));
        $this->assertEquals($banner->product->is_active, $response->json('data.singleProductCategory.banners.0.product.is_active'));
        $this->assertEquals($banner->product->cost, $response->json('data.singleProductCategory.banners.0.product.cost'));
        $this->assertEquals($banner->product->actual_price, $response->json('data.singleProductCategory.banners.0.product.actual_price'));
        $this->assertEquals($banner->product->old_price, $response->json('data.singleProductCategory.banners.0.product.old_price'));
        $this->assertEquals($banner->product->category_id, $response->json('data.singleProductCategory.banners.0.product.category_id'));
        $this->assertEquals($banner->product->brand_id, $response->json('data.singleProductCategory.banners.0.product.brand_id'));
        $this->assertEquals($banner->product->created_at->timestamp, $response->json('data.singleProductCategory.banners.0.product.created_at'));
        $this->assertEquals($banner->product->updated_at->timestamp, $response->json('data.singleProductCategory.banners.0.product.updated_at'));
        $this->assertEquals($banner->product->deleted_at?->timestamp, $response->json('data.singleProductCategory.banners.0.product.deleted_at'));
    }
}
