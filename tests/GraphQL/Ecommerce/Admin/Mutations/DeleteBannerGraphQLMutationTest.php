<?php

namespace Tests\GraphQL\Ecommerce\Admin\Mutations;

use App\Enums\Company\CompanyStatusEnum;
use App\Models\Company\Ecommerce\Banner;
use Illuminate\Support\Arr;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class DeleteBannerGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    #[Test]
    public function successDeleteBanner()
    {
        Banner::factory()->count(10)->create();
        $bannerIds = Banner::query()->inRandomOrder()->take(5)->pluck('id');
        $remainingBannerIds = Banner::query()->whereNotIn('id', $bannerIds)->pluck('id');

        $response = $this
            ->actingAs($this->createAdminUser())
            ->post(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['ids' => $bannerIds]));

        $response->assertOk();
        $response->assertJsonStructure(['data' => ['deleteBanner']]);

        $this->assertTrue($response->json('data.deleteBanner'));
        $this->assertTrue(Banner::query()->whereIn('id', $bannerIds)->doesntExist());
        $this->assertEquals($remainingBannerIds->count(), Banner::query()->count());
        $this->assertTrue(Banner::query()->whereIn('id', $remainingBannerIds)->exists());
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
    public function errorDeleteBannerWithInactiveCompany(): void
    {
        tenant()->updateOrFail(['status' => CompanyStatusEnum::INACTIVE]);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->post(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['ids' => [Banner::factory()->create()->id]]));

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals(trans('api.inactive_company'), $response->json('errors.0.message'));
    }

    private function buildGraphQLQuery(array $variables): array
    {
        return [
            'query' => <<<'GRAPHQL'
                mutation DeleteBanner($ids: [Int]!) {
                    deleteBanner(ids: $ids)
                }
            GRAPHQL,
            'variables' => $variables,
        ];
    }
}
