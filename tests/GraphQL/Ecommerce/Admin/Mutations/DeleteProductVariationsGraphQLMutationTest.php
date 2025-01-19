<?php

namespace Tests\GraphQL\Ecommerce\Admin\Mutations;

use App\Enums\Company\CompanyStatusEnum;
use App\Models\Company\Ecommerce\EcommerceImage;
use App\Models\Company\Ecommerce\ProductOptionValue;
use App\Models\Company\Ecommerce\ProductVariation;
use Illuminate\Support\Arr;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class DeleteProductVariationsGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    #[Test]
    public function successDeleteProductVariations()
    {
        ProductVariation::factory()->count(10)->create();
        $productVariationIds = ProductVariation::query()->inRandomOrder()->take(5)->pluck('id');
        $remainingProductVariationIds = ProductVariation::query()->whereNotIn('id', $productVariationIds)->pluck('id');

        $response = $this
            ->actingAs($this->createAdminUser())
            ->post(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['ids' => $productVariationIds]));

        $response->assertOk();
        $response->assertJsonStructure(['data' => ['deleteProductVariations']]);

        $this->assertTrue($response->json('data.deleteProductVariations'));
        $this->assertTrue(ProductVariation::query()->whereIn('id', $productVariationIds)->doesntExist());
        $this->assertEquals($remainingProductVariationIds->count(), ProductVariation::query()->count());
        $this->assertTrue(ProductVariation::query()->whereIn('id', $remainingProductVariationIds)->exists());
    }

    #[Test]
    public function successDeleteProductVariationsAndRelations()
    {
        /** @var ProductVariation $variation */
        $variation = ProductVariation::factory()->create();

        /** @var EcommerceImage $image */
        $image = EcommerceImage::factory()->create();

        /** @var ProductOptionValue $value */
        $value = ProductOptionValue::factory()->create();

        $variation->optionValues()->attach($value, ['option_id' => $value->option_id, 'product_id' => $variation->product_id]);
        $variation->images()->attach($image);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->post(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['ids' => $variation->id]));

        $response->assertOk();
        $response->assertJsonStructure(['data' => ['deleteProductVariations']]);
        $this->assertTrue($response->json('data.deleteProductVariations'));
        $this->assertTrue(ProductVariation::query()->where('id', $variation->id)->doesntExist());
        $this->assertDatabaseMissing('ecommerce_imageables', [
            'image_id' => $image->id,
            'imageable_id' => $variation->id,
            'imageable_type' => ProductVariation::class,
        ]);
        $this->assertDatabaseMissing('ecommerce_product_join_options', [
            'option_id' => $value->option_id,
            'value_id' => $value->id,
            'product_id' => $variation->product_id,
            'product_variation_id' => $variation->id,
        ]);
    }

    #[Test]
    public function errorValidation(): void
    {
        // GraphQL type errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->post(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['ids' => null]));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$ids" of non-null type "[Int]!" must not be null.'));

        // Rule errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->post(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['ids' => []]));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.required', ['attribute' => 'ids']), $response->json('errors.0.extensions.validation.ids.0'));

        $response = $this
            ->actingAs($this->createAdminUser())
            ->post(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['ids' => [999999999]]));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'ids.0']), $response->json('errors.0.extensions.validation')['ids.0'][0]);
    }

    #[Test]
    public function errorDeleteProductVariationsWithInactiveCompany(): void
    {
        tenant()->updateOrFail(['status' => CompanyStatusEnum::INACTIVE]);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->post(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['ids' => [ProductVariation::factory()->create()->id]]));

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals(trans('api.inactive_company'), $response->json('errors.0.message'));
    }

    private function buildGraphQLQuery(array $variables): array
    {
        return [
            'query' => <<<'GRAPHQL'
                mutation DeleteProductVariations($ids: [Int]!) {
                    deleteProductVariations(ids: $ids)
                }
            GRAPHQL,
            'variables' => $variables,
        ];
    }
}
