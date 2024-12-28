<?php

namespace Tests\GraphQL\Ecommerce\Admin\Mutations;

use App\Enums\Company\CompanyStatusEnum;
use App\Models\Company\Ecommerce\ProductJoinOption;
use App\Models\Company\Ecommerce\ProductOptionValue;
use Illuminate\Support\Arr;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class DeleteProductOptionValueGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    #[Test]
    public function successDeleteProductOptionValue()
    {
        ProductOptionValue::factory()->count(10)->create();
        $valueIds = ProductOptionValue::query()->inRandomOrder()->take(5)->pluck('id');
        $remainingValueIds = ProductOptionValue::query()->whereNotIn('id', $valueIds)->pluck('id');

        $response = $this
            ->actingAs($this->createAdminUser())
            ->post(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['ids' => $valueIds]));

        $response->assertOk();
        $response->assertJsonStructure(['data' => ['deleteProductOptionValue']]);

        $this->assertTrue($response->json('data.deleteProductOptionValue'));
        $this->assertTrue(ProductOptionValue::query()->whereIn('id', $valueIds)->doesntExist());
        $this->assertEquals($remainingValueIds->count(), ProductOptionValue::query()->count());
        $this->assertTrue(ProductOptionValue::query()->whereIn('id', $remainingValueIds)->exists());
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
        $this->assertNotNull(Arr::first($response->json('errors'), fn($item) => $item['message'] === 'Variable "$ids" of non-null type "[Int]!" must not be null.'));

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

        /** @var ProductJoinOption $productJoinOption */
        $productJoinOption = ProductJoinOption::factory()->create();
        $response = $this
            ->actingAs($this->createAdminUser())
            ->post(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['ids' => [$productJoinOption->value_id]]));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(
            trans('api.are used', ['item' => trans('ecommerce.product_option_value'), 'element' => trans('ecommerce.product_join_option')]),
            $response->json('errors.0.extensions.validation')['ids.0'][0]
        );
    }

    #[Test]
    public function errorDeleteProductOptionValueWithInactiveCompany(): void
    {
        tenant()->updateOrFail(['status' => CompanyStatusEnum::INACTIVE]);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->post(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['ids' => [ProductOptionValue::factory()->create()->id]]));

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals(trans('api.inactive_company'), $response->json('errors.0.message'));
    }

    private function buildGraphQLQuery(array $variables): array
    {
        return [
            'query' => <<<'GRAPHQL'
                mutation DeleteProductOptionValue($ids: [Int]!) {
                    deleteProductOptionValue(ids: $ids)
                }
            GRAPHQL,
            'variables' => $variables,
        ];
    }
}
