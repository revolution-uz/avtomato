<?php

namespace Tests\GraphQL\Ecommerce\Common\Queries;

use App\Models\Company\Ecommerce\EcommerceImage;
use App\Models\Company\Ecommerce\Product;
use App\Models\Company\Ecommerce\ProductJoinOption;
use App\Models\Company\Ecommerce\ProductOption;
use App\Models\Company\Ecommerce\ProductOptionValue;
use App\Models\Company\Ecommerce\ProductVariation;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class SingleProductGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private array $_structure = [
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
            'slug',
            'is_active',
            'image_id',
            'created_at',
            'updated_at',
            'deleted_at',
        ],
        'images' => [
            '*' => [
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
        'options' => [
            '*' => [
                'id',
                'name',
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
        ],
        'variations' => [
            '*' => [
                'id',
                'is_active',
                'product_id',
                'created_at',
                'updated_at',
                'deleted_at',
                'images' => [
                    '*' => [
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
                'option_values' => [
                    '*' => [
                        'id',
                        'value',
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
                            'name',
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
                    ],
                ],
            ],
        ],
    ];

    #[Test]
    public function successSingleProduct()
    {
        /** @var Product $product */
        $product = Product::factory()->create();

        $images = EcommerceImage::factory()->count(3)->create();
        $product->images()->attach(
            $images->pluck('id')->mapWithKeys(fn ($id) => [$id => ['order' => $id]])
        );

        $options = ProductOption::factory()->count(3)->create()->each(
            fn (ProductOption $option) => ProductOptionValue::factory()->count(3)->create(['option_id' => $option->id])
        );

        $variations = ProductVariation::factory()->count(3)->create(['product_id' => $product->id]);

        $variations->each(
            fn (ProductVariation $variation) => $variation->images()
                ->attach(
                    $images->pluck('id')->mapWithKeys(fn ($id) => [$id => ['order' => $variation->id]])
                )
        );

        $options->each(function (ProductOption $option) use ($variations, $product) {
            $option->values->each(function (ProductOptionValue $value) use ($option, $variations, $product) {
                $variations->each(
                    fn (ProductVariation $variation) => ProductJoinOption::create([
                        'option_id' => $option->id,
                        'value_id' => $value->id,
                        'product_id' => $product->id,
                        'product_variation_id' => $variation->id,
                    ])
                );
            });
        });

        $response = $this->postJson(
            route('graphql.ecommerce-common'), $this->buildGraphQLQuery(['id' => $product->id])
        );

        $this->assertSuccessSingleProductResponse($response, $product);
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
                query SingleProduct(\$id: Int!) {
                    singleProduct(id: \$id) {
                        {$this->convertIntoQuery($this->_structure)}
                    }
                }
            GRAPHQL,
            'variables' => $variables,
        ];
    }

    private function assertSuccessSingleProductResponse(TestResponse $response, Product $product): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure(['data' => ['singleProduct' => $this->_structure]]);

        $this->assertEquals($product->id, $response->json('data.singleProduct.id'));
        $this->assertEquals($product->name, $response->json('data.singleProduct.name'));
        $this->assertEquals($product->name_uz, $response->json('data.singleProduct.name_uz'));
        $this->assertEquals($product->name_ru, $response->json('data.singleProduct.name_ru'));
        $this->assertEquals($product->name_en, $response->json('data.singleProduct.name_en'));
        $this->assertEquals($product->name_oz, $response->json('data.singleProduct.name_oz'));
        $this->assertEquals($product->slug, $response->json('data.singleProduct.slug'));
        $this->assertEquals($product->cost, $response->json('data.singleProduct.cost'));
        $this->assertEquals($product->actual_price, $response->json('data.singleProduct.actual_price'));
        $this->assertEquals($product->old_price, $response->json('data.singleProduct.old_price'));
        $this->assertEquals($product->is_active, $response->json('data.singleProduct.is_active'));
        $this->assertEquals($product->category_id, $response->json('data.singleProduct.category_id'));
        $this->assertEquals($product->brand_id, $response->json('data.singleProduct.brand_id'));
        $this->assertEquals($product->created_at->timestamp, $response->json('data.singleProduct.created_at'));
        $this->assertEquals($product->updated_at->timestamp, $response->json('data.singleProduct.updated_at'));
        $this->assertEquals($product->deleted_at?->timestamp, $response->json('data.singleProduct.deleted_at'));

        // category
        $this->assertEquals($product->category->id, $response->json('data.singleProduct.category.id'));
        $this->assertEquals($product->category->name, $response->json('data.singleProduct.category.name'));
        $this->assertEquals($product->category->name_uz, $response->json('data.singleProduct.category.name_uz'));
        $this->assertEquals($product->category->name_ru, $response->json('data.singleProduct.category.name_ru'));
        $this->assertEquals($product->category->name_en, $response->json('data.singleProduct.category.name_en'));
        $this->assertEquals($product->category->name_oz, $response->json('data.singleProduct.category.name_oz'));
        $this->assertEquals($product->category->is_active, $response->json('data.singleProduct.category.is_active'));
        $this->assertEquals($product->category->parent_id, $response->json('data.singleProduct.category.parent_id'));
        $this->assertEquals($product->category->image_id, $response->json('data.singleProduct.category.image_id'));
        $this->assertEquals($product->category->created_at->timestamp, $response->json('data.singleProduct.category.created_at'));
        $this->assertEquals($product->category->updated_at->timestamp, $response->json('data.singleProduct.category.updated_at'));
        $this->assertEquals($product->category->deleted_at?->timestamp, $response->json('data.singleProduct.category.deleted_at'));

        // brand
        $this->assertEquals($product->brand->id, $response->json('data.singleProduct.brand.id'));
        $this->assertEquals($product->brand->name, $response->json('data.singleProduct.brand.name'));
        $this->assertEquals($product->brand->name_uz, $response->json('data.singleProduct.brand.name_uz'));
        $this->assertEquals($product->brand->name_ru, $response->json('data.singleProduct.brand.name_ru'));
        $this->assertEquals($product->brand->name_en, $response->json('data.singleProduct.brand.name_en'));
        $this->assertEquals($product->brand->name_oz, $response->json('data.singleProduct.brand.name_oz'));
        $this->assertEquals($product->brand->slug, $response->json('data.singleProduct.brand.slug'));
        $this->assertEquals($product->brand->is_active, $response->json('data.singleProduct.brand.is_active'));
        $this->assertEquals($product->brand->image_id, $response->json('data.singleProduct.brand.image_id'));
        $this->assertEquals($product->brand->created_at->timestamp, $response->json('data.singleProduct.brand.created_at'));
        $this->assertEquals($product->brand->updated_at->timestamp, $response->json('data.singleProduct.brand.updated_at'));
        $this->assertEquals($product->brand->deleted_at?->timestamp, $response->json('data.singleProduct.brand.deleted_at'));

        // images
        $this->assertEquals($product->images()->count(), count($response->json('data.singleProduct.images')));
        $this->assertEquals($product->images[0]->id, $response->json('data.singleProduct.images.0.id'));
        $this->assertEquals($product->images[0]->url, $response->json('data.singleProduct.images.0.url'));
        $this->assertEquals($product->images[0]->original_name, $response->json('data.singleProduct.images.0.original_name'));
        $this->assertEquals($product->images[0]->mime, $response->json('data.singleProduct.images.0.mime'));
        $this->assertEquals($product->images[0]->pivot->order, $response->json('data.singleProduct.images.0.order'));
        $this->assertEquals($product->images[0]->created_at->timestamp, $response->json('data.singleProduct.images.0.created_at'));
        $this->assertEquals($product->images[0]->updated_at->timestamp, $response->json('data.singleProduct.images.0.updated_at'));
        $this->assertEquals($product->images[0]->deleted_at?->timestamp, $response->json('data.singleProduct.images.0.deleted_at'));

        // options
        $this->assertEquals($product->options->count(), count($response->json('data.singleProduct.options')));
        $this->assertEquals($product->options[0]->id, $response->json('data.singleProduct.options.0.id'));
        $this->assertEquals($product->options[0]->name, $response->json('data.singleProduct.options.0.name'));
        $this->assertEquals($product->options[0]->name_uz, $response->json('data.singleProduct.options.0.name_uz'));
        $this->assertEquals($product->options[0]->name_ru, $response->json('data.singleProduct.options.0.name_ru'));
        $this->assertEquals($product->options[0]->name_en, $response->json('data.singleProduct.options.0.name_en'));
        $this->assertEquals($product->options[0]->name_oz, $response->json('data.singleProduct.options.0.name_oz'));
        $this->assertEquals($product->options[0]->slug, $response->json('data.singleProduct.options.0.slug'));
        $this->assertEquals($product->options[0]->description, $response->json('data.singleProduct.options.0.description'));
        $this->assertEquals($product->options[0]->created_at->timestamp, $response->json('data.singleProduct.options.0.created_at'));
        $this->assertEquals($product->options[0]->updated_at->timestamp, $response->json('data.singleProduct.options.0.updated_at'));
        $this->assertEquals($product->options[0]->deleted_at?->timestamp, $response->json('data.singleProduct.options.0.deleted_at'));

        // variations
        $this->assertEquals($product->variations()->count(), count($response->json('data.singleProduct.variations')));
        /** @var ProductVariation $variation */
        $variation = $product->variations[0];
        $this->assertEquals($variation->id, $response->json('data.singleProduct.variations.0.id'));
        $this->assertEquals($variation->product_id, $response->json('data.singleProduct.variations.0.product_id'));
        $this->assertEquals($variation->is_active, $response->json('data.singleProduct.variations.0.is_active'));
        $this->assertEquals($variation->created_at->timestamp, $response->json('data.singleProduct.variations.0.created_at'));
        $this->assertEquals($variation->updated_at->timestamp, $response->json('data.singleProduct.variations.0.updated_at'));
        $this->assertEquals($variation->deleted_at?->timestamp, $response->json('data.singleProduct.variations.0.deleted_at'));

        // variations -> images
        $this->assertEquals($variation->images()->count(), count($response->json('data.singleProduct.variations.0.images')));
        $this->assertEquals($variation->images[0]->id, $response->json('data.singleProduct.variations.0.images.0.id'));
        $this->assertEquals($variation->images[0]->url, $response->json('data.singleProduct.variations.0.images.0.url'));
        $this->assertEquals($variation->images[0]->original_name, $response->json('data.singleProduct.variations.0.images.0.original_name'));
        $this->assertEquals($variation->images[0]->mime, $response->json('data.singleProduct.variations.0.images.0.mime'));
        $this->assertEquals($variation->images[0]->pivot->order, $response->json('data.singleProduct.variations.0.images.0.order'));
        $this->assertEquals($variation->images[0]->created_at->timestamp, $response->json('data.singleProduct.variations.0.images.0.created_at'));
        $this->assertEquals($variation->images[0]->updated_at->timestamp, $response->json('data.singleProduct.variations.0.images.0.updated_at'));
        $this->assertEquals($variation->images[0]->deleted_at?->timestamp, $response->json('data.singleProduct.variations.0.images.0.deleted_at'));

        // variations -> values
        $this->assertEquals($variation->optionValues()->count(), count($response->json('data.singleProduct.variations.0.option_values')));
        /** @var ProductOptionValue $value */
        $value = $variation->optionValues[0];
        $this->assertEquals($value->id, $response->json('data.singleProduct.variations.0.option_values.0.id'));
        $this->assertEquals($value->value, $response->json('data.singleProduct.variations.0.option_values.0.value'));
        $this->assertEquals($value->value_uz, $response->json('data.singleProduct.variations.0.option_values.0.value_uz'));
        $this->assertEquals($value->value_ru, $response->json('data.singleProduct.variations.0.option_values.0.value_ru'));
        $this->assertEquals($value->value_en, $response->json('data.singleProduct.variations.0.option_values.0.value_en'));
        $this->assertEquals($value->value_oz, $response->json('data.singleProduct.variations.0.option_values.0.value_oz'));
        $this->assertEquals($value->slug, $response->json('data.singleProduct.variations.0.option_values.0.slug'));
        $this->assertEquals($value->option_id, $response->json('data.singleProduct.variations.0.option_values.0.option_id'));
        $this->assertEquals($value->image_id, $response->json('data.singleProduct.variations.0.option_values.0.image_id'));
        $this->assertEquals($value->created_at->timestamp, $response->json('data.singleProduct.variations.0.option_values.0.created_at'));
        $this->assertEquals($value->updated_at->timestamp, $response->json('data.singleProduct.variations.0.option_values.0.updated_at'));
        $this->assertEquals($value->deleted_at?->timestamp, $response->json('data.singleProduct.variations.0.option_values.0.deleted_at'));

        // variations -> option_values -> option
        $this->assertEquals($value->option->id, $response->json('data.singleProduct.variations.0.option_values.0.option.id'));
        $this->assertEquals($value->option->name, $response->json('data.singleProduct.variations.0.option_values.0.option.name'));
        $this->assertEquals($value->option->name_uz, $response->json('data.singleProduct.variations.0.option_values.0.option.name_uz'));
        $this->assertEquals($value->option->name_ru, $response->json('data.singleProduct.variations.0.option_values.0.option.name_ru'));
        $this->assertEquals($value->option->name_en, $response->json('data.singleProduct.variations.0.option_values.0.option.name_en'));
        $this->assertEquals($value->option->name_oz, $response->json('data.singleProduct.variations.0.option_values.0.option.name_oz'));
        $this->assertEquals($value->option->slug, $response->json('data.singleProduct.variations.0.option_values.0.option.slug'));
        $this->assertEquals($value->option->description, $response->json('data.singleProduct.variations.0.option_values.0.option.description'));
        $this->assertEquals($value->option->created_at->timestamp, $response->json('data.singleProduct.variations.0.option_values.0.option.created_at'));
        $this->assertEquals($value->option->updated_at->timestamp, $response->json('data.singleProduct.variations.0.option_values.0.option.updated_at'));
        $this->assertEquals($value->option->deleted_at?->timestamp, $response->json('data.singleProduct.variations.0.option_values.0.option.deleted_at'));
    }
}
