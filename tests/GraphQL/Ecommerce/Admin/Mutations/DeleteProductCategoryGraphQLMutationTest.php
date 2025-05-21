<?php

namespace Tests\GraphQL\Ecommerce\Admin\Mutations;

use App\Enums\Company\CompanyStatusEnum;
use App\Models\Company\Ecommerce\Product;
use App\Models\Company\Ecommerce\ProductCategory;
use Illuminate\Support\Arr;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class DeleteProductCategoryGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    #[Test]
    public function successDeleteProductCategory()
    {
        ProductCategory::factory()->count(10)->create();
        $productCategoryIds = ProductCategory::query()->inRandomOrder()->take(5)->pluck('id');
        $remainingProductCategoryIds = ProductCategory::query()->whereNotIn('id', $productCategoryIds)->pluck('id');

        $response = $this
            ->actingAs($this->createAdminUser())
            ->post(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['ids' => $productCategoryIds]));

        $response->assertOk();
        $response->assertJsonStructure(['data' => ['deleteProductCategory']]);

        $this->assertTrue($response->json('data.deleteProductCategory'));
        $this->assertTrue(ProductCategory::query()->whereIn('id', $productCategoryIds)->doesntExist());
        $this->assertEquals($remainingProductCategoryIds->count(), ProductCategory::query()->count());
        $this->assertTrue(ProductCategory::query()->whereIn('id', $remainingProductCategoryIds)->exists());
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

        // rule errors
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
        $this->assertEquals(trans('api.not found', ['attribute' => 'ids']), $response->json('errors.0.extensions.validation')['ids.0'][0]);

        $product = Product::factory()->create();
        $response = $this
            ->actingAs($this->createAdminUser())
            ->post(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['ids' => [$product->category_id]]));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(
            trans('api.cannot_delete', ['item' => trans('ecommerce.category'), 'element' => trans('ecommerce.product')]),
            $response->json('errors.0.extensions.validation')['ids.0'][0]
        );
    }

    #[Test]
    public function errorDeleteProductCategoryWithInactiveCompany(): void
    {
        tenant()->updateOrFail(['status' => CompanyStatusEnum::INACTIVE]);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->post(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['ids' => [ProductCategory::factory()->create()->id]]));

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals(trans('api.inactive_company'), $response->json('errors.0.message'));
    }

    private function buildGraphQLQuery(array $variables): array
    {
        return [
            'query' => <<<'GRAPHQL'
                mutation DeleteProductCategory($ids: [Int]!) {
                    deleteProductCategory(ids: $ids)
                }
            GRAPHQL,
            'variables' => $variables,
        ];
    }
}
