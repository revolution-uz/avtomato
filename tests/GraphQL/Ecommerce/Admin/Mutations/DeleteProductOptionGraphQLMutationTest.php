<?php

namespace Tests\GraphQL\Ecommerce\Admin\Mutations;

use App\Enums\Company\CompanyStatusEnum;
use App\Models\Company\Ecommerce\ProductJoinOption;
use App\Models\Company\Ecommerce\ProductOption;
use Illuminate\Support\Arr;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class DeleteProductOptionGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    #[Test]
    public function successDeleteProductOption()
    {
        ProductOption::factory()->count(10)->create();
        $optionIds = ProductOption::query()->inRandomOrder()->take(5)->pluck('id');
        $remainingOptionIds = ProductOption::query()->whereNotIn('id', $optionIds)->pluck('id');

        $response = $this
            ->actingAs($this->createAdminUser())
            ->post(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['ids' => $optionIds]));

        $response->assertOk();
        $response->assertJsonStructure(['data' => ['deleteProductOption']]);

        $this->assertTrue($response->json('data.deleteProductOption'));
        $this->assertTrue(ProductOption::query()->whereIn('id', $optionIds)->doesntExist());
        $this->assertEquals($remainingOptionIds->count(), ProductOption::query()->count());
        $this->assertTrue(ProductOption::query()->whereIn('id', $remainingOptionIds)->exists());
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

        /** @var ProductJoinOption $productJoinOption */
        $productJoinOption = ProductJoinOption::factory()->create();
        $response = $this
            ->actingAs($this->createAdminUser())
            ->post(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['ids' => [$productJoinOption->option_id]]));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(
            trans('api.cannot_delete', ['item' => trans('ecommerce.product_option_value'), 'element' => trans('ecommerce.product_join_option')]),
            $response->json('errors.0.extensions.validation')['ids.0'][0]
        );
    }

    #[Test]
    public function errorDeleteProductOptionWithInactiveCompany(): void
    {
        tenant()->updateOrFail(['status' => CompanyStatusEnum::INACTIVE]);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->post(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['ids' => [ProductOption::factory()->create()->id]]));

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals(trans('api.inactive_company'), $response->json('errors.0.message'));
    }

    private function buildGraphQLQuery(array $variables): array
    {
        return [
            'query' => <<<'GRAPHQL'
                mutation DeleteProductOption($ids: [Int]!) {
                    deleteProductOption(ids: $ids)
                }
            GRAPHQL,
            'variables' => $variables,
        ];
    }
}
