<?php

namespace Tests\GraphQL\Ecommerce\Admin\Mutations;

use App\Enums\Company\CompanyStatusEnum;
use App\Models\Company\Ecommerce\Product;
use Illuminate\Support\Arr;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class DeleteProductGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    #[Test]
    public function successDeleteProduct()
    {
        Product::factory()->count(10)->create();
        $productIds = Product::query()->inRandomOrder()->take(5)->pluck('id');
        $remainingProductIds = Product::query()->whereNotIn('id', $productIds)->pluck('id');

        $response = $this
            ->actingAs($this->createAdminUser())
            ->post(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['ids' => $productIds]));

        $response->assertOk();
        $response->assertJsonStructure(['data' => ['deleteProduct']]);

        $this->assertTrue($response->json('data.deleteProduct'));
        $this->assertTrue(Product::query()->whereIn('id', $productIds)->doesntExist());
        $this->assertEquals($remainingProductIds->count(), Product::query()->count());
        $this->assertTrue(Product::query()->whereIn('id', $remainingProductIds)->exists());
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
    public function errorDeleteProductWithInactiveCompany(): void
    {
        tenant()->updateOrFail(['status' => CompanyStatusEnum::INACTIVE]);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->post(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['ids' => [Product::factory()->create()->id]]));

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals(trans('api.inactive_company'), $response->json('errors.0.message'));
    }

    private function buildGraphQLQuery(array $variables): array
    {
        return [
            'query' => <<<'GRAPHQL'
                mutation DeleteProduct($ids: [Int]!) {
                    deleteProduct(ids: $ids)
                }
            GRAPHQL,
            'variables' => $variables,
        ];
    }
}
