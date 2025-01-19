<?php

namespace Tests\GraphQL\Ecommerce\Admin\Mutations;

use App\Enums\Company\CompanyStatusEnum;
use App\Models\Company\Ecommerce\Banner;
use App\Models\Company\Ecommerce\EcommerceImage;
use App\Models\Company\Ecommerce\Product;
use App\Models\Company\Ecommerce\ProductBrand;
use App\Models\Company\Ecommerce\ProductCategory;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class UpdateBannerGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private array $_structure = [
        'id',
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
            'parent_id',
            'name_uz',
            'name_ru',
            'name_en',
            'name_oz',
            'is_active',
            'created_at',
            'updated_at',
            'deleted_at',
        ],
        'brand' => [
            'id',
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
            'category_id',
            'brand_id',
            'name_uz',
            'name_ru',
            'name_en',
            'name_oz',
            'slug',
            'is_active',
            'cost',
            'actual_price',
            'old_price',
            'created_at',
            'updated_at',
            'deleted_at',
        ],
    ];

    #[Test]
    public function successUpdateBanner()
    {
        /** @var Banner $banner */
        $banner = Banner::factory()->create();

        $variables = ['id' => $banner->id, 'banner' => $this->getInputVariables()];

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery($variables));

        $this->assertSuccessUpdateBannerResponse($response, $banner, $variables);
    }

    #[Test]
    public function successUpdateBannerWithOnlyRequiredAttributes()
    {
        /** @var Banner $banner */
        $banner = Banner::factory()->create();

        $variables = ['id' => $banner->id, 'banner' => $this->getInputVariables(true)];

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery($variables));

        $this->assertSuccessUpdateBannerResponse($response, $banner, $variables, true);
    }

    #[Test]
    public function errorValidation(): void
    {
        // GraphQL type errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery());
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$id" of required type "Int!" was not provided.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$banner" of required type "BannerInputGraphQLType!" was not provided.'));

        // rule errors
        $variables = $this->getInputVariables(false, [
            'name_uz' => Str::random(256),
            'name_ru' => Str::random(256),
            'name_en' => Str::random(256),
            'name_oz' => Str::random(256),
            'url' => Str::random(256),
            'order' => -1,
            'starts_at' => 'abc',
            'ends_at' => 'abc',
            'image_uz_id' => 999999999,
            'image_ru_id' => 999999999,
            'image_en_id' => 999999999,
            'image_oz_id' => 999999999,
            'category_id' => 999999999,
            'brand_id' => 999999999,
            'product_id' => 999999999,
        ]);
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['id' => 999999999, 'banner' => $variables]));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'id']), $response->json('errors.0.extensions.validation.id.0'));
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'banner.name uz', 'max' => 255]), $response->json('errors.0.extensions.validation')['banner.name_uz'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'banner.name ru', 'max' => 255]), $response->json('errors.0.extensions.validation')['banner.name_ru'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'banner.name en', 'max' => 255]), $response->json('errors.0.extensions.validation')['banner.name_en'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'banner.name oz', 'max' => 255]), $response->json('errors.0.extensions.validation')['banner.name_oz'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'banner.url', 'max' => 255]), $response->json('errors.0.extensions.validation')['banner.url'][0]);
        $this->assertEquals(trans('validation.min.numeric', ['attribute' => 'banner.order', 'min' => 1]), $response->json('errors.0.extensions.validation')['banner.order'][0]);
        $this->assertEquals(trans('validation.date_format', ['attribute' => 'banner.starts at', 'format' => 'Y-m-d H:i:s']), $response->json('errors.0.extensions.validation')['banner.starts_at'][0]);
        $this->assertEquals(trans('validation.date_format', ['attribute' => 'banner.ends at', 'format' => 'Y-m-d H:i:s']), $response->json('errors.0.extensions.validation')['banner.ends_at'][0]);
        $this->assertEquals(trans('validation.exists', ['attribute' => 'banner.image uz id']), $response->json('errors.0.extensions.validation')['banner.image_uz_id'][0]);
        $this->assertEquals(trans('validation.exists', ['attribute' => 'banner.image ru id']), $response->json('errors.0.extensions.validation')['banner.image_ru_id'][0]);
        $this->assertEquals(trans('validation.exists', ['attribute' => 'banner.image en id']), $response->json('errors.0.extensions.validation')['banner.image_en_id'][0]);
        $this->assertEquals(trans('validation.exists', ['attribute' => 'banner.image oz id']), $response->json('errors.0.extensions.validation')['banner.image_oz_id'][0]);
        $this->assertEquals(trans('validation.exists', ['attribute' => 'banner.category id']), $response->json('errors.0.extensions.validation')['banner.category_id'][0]);
        $this->assertEquals(trans('validation.exists', ['attribute' => 'banner.brand id']), $response->json('errors.0.extensions.validation')['banner.brand_id'][0]);
        $this->assertEquals(trans('validation.exists', ['attribute' => 'banner.product id']), $response->json('errors.0.extensions.validation')['banner.product_id'][0]);
    }

    #[Test]
    public function errorUpdateBannerWithInactiveCompany(): void
    {
        tenant()->updateOrFail(['status' => CompanyStatusEnum::INACTIVE]);

        /** @var Banner $banner */
        $banner = Banner::factory()->create();

        $variables = ['id' => $banner->id, 'banner' => $this->getInputVariables()];

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery($variables));

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals(trans('api.inactive_company'), $response->json('errors.0.message'));
    }

    protected function buildGraphQLQuery(array $variables = []): array
    {
        return [
            'query' => <<<GRAPHQL
                mutation UpdateBanner(\$id: Int!, \$banner: BannerInputGraphQLType!) {
                    updateBanner(id: \$id, banner: \$banner) {
                        {$this->convertIntoQuery($this->_structure)}
                    }
                }
            GRAPHQL,
            'variables' => $variables,
        ];
    }

    private function getInputVariables(bool $isRequired = false, array $variables = [])
    {
        return array_merge(
            [
                'name_uz' => Str::random(255),
                'name_ru' => Str::random(255),
                'name_en' => Str::random(255),
                'name_oz' => Str::random(255),
                'url' => ! $isRequired ? Str::random(255) : null,
                'is_active' => (bool) random_int(0, 1),
                'order' => ! $isRequired ? random_int(1, 999) : null,
                'starts_at' => ! $isRequired ? Carbon::now()->subDays(random_int(1, 999))->format('Y-m-d H:i:s') : null,
                'ends_at' => ! $isRequired ? Carbon::now()->addDays(random_int(1, 999))->format('Y-m-d H:i:s') : null,
                'category_id' => ! $isRequired ? ProductCategory::factory()->create()->id : null,
                'brand_id' => ! $isRequired ? ProductBrand::factory()->create()->id : null,
                'product_id' => ! $isRequired ? Product::factory()->create()->id : null,
                'image_uz_id' => EcommerceImage::factory()->create()->id,
                'image_ru_id' => EcommerceImage::factory()->create()->id,
                'image_en_id' => EcommerceImage::factory()->create()->id,
                'image_oz_id' => EcommerceImage::factory()->create()->id,
            ],
            $variables
        );
    }

    private function assertSuccessUpdateBannerResponse(TestResponse $response, Banner $banner, array $variables, bool $isRequired = false): void
    {
        $response->assertOk();
        ! $isRequired ?
            $response->assertJsonStructure(['data' => ['updateBanner' => $this->_structure]]) :
            $response->assertJsonStructure(['data' => ['updateBanner' => Arr::except($this->_structure, ['category', 'brand', 'product'])]]);

        $banner->refresh();

        $this->assertEquals($banner->name_uz, $variables['banner']['name_uz']);
        $this->assertEquals($banner->name_ru, $variables['banner']['name_ru']);
        $this->assertEquals($banner->name_en, $variables['banner']['name_en']);
        $this->assertEquals($banner->name_oz, $variables['banner']['name_oz']);
        $this->assertEquals($banner->is_active, $variables['banner']['is_active']);
        $this->assertEquals($banner->url, $variables['banner']['url']);
        $this->assertEquals($banner->category_id, $variables['banner']['category_id']);
        $this->assertEquals($banner->brand_id, $variables['banner']['brand_id']);
        $this->assertEquals($banner->product_id, $variables['banner']['product_id']);
        $this->assertEquals($banner->image_uz_id, $variables['banner']['image_uz_id']);
        $this->assertEquals($banner->image_ru_id, $variables['banner']['image_ru_id']);
        $this->assertEquals($banner->image_en_id, $variables['banner']['image_en_id']);
        $this->assertEquals($banner->image_oz_id, $variables['banner']['image_oz_id']);

        if (! $isRequired) {
            $this->assertEquals($banner->url, $variables['banner']['url']);
            $this->assertEquals($banner->order, $variables['banner']['order']);
            $this->assertEquals($banner->starts_at->format('Y-m-d H:i:s'), $variables['banner']['starts_at']);
            $this->assertEquals($banner->ends_at->format('Y-m-d H:i:s'), $variables['banner']['ends_at']);
            $this->assertEquals($banner->category_id, $variables['banner']['category_id']);
            $this->assertEquals($banner->brand_id, $variables['banner']['brand_id']);
            $this->assertEquals($banner->product_id, $variables['banner']['product_id']);
        } else {
            $this->assertNull($banner->url);
            $this->assertNull($banner->order);
            $this->assertNull($banner->starts_at);
            $this->assertNull($banner->ends_at);
            $this->assertNull($banner->category_id);
            $this->assertNull($banner->brand_id);
            $this->assertNull($banner->product_id);
        }

        $this->assertEquals($banner->id, $response->json('data.updateBanner.id'));
        $this->assertEquals($banner->name_uz, $response->json('data.updateBanner.name_uz'));
        $this->assertEquals($banner->name_ru, $response->json('data.updateBanner.name_ru'));
        $this->assertEquals($banner->name_en, $response->json('data.updateBanner.name_en'));
        $this->assertEquals($banner->name_oz, $response->json('data.updateBanner.name_oz'));
        $this->assertEquals($banner->is_active, $response->json('data.updateBanner.is_active'));
        $this->assertEquals($banner->url, $response->json('data.updateBanner.url'));
        $this->assertEquals($banner->image_uz_id, $response->json('data.updateBanner.image_uz_id'));
        $this->assertEquals($banner->image_ru_id, $response->json('data.updateBanner.image_ru_id'));
        $this->assertEquals($banner->image_en_id, $response->json('data.updateBanner.image_en_id'));
        $this->assertEquals($banner->image_oz_id, $response->json('data.updateBanner.image_oz_id'));
        $this->assertEquals($banner->category_id, $response->json('data.updateBanner.category_id'));
        $this->assertEquals($banner->brand_id, $response->json('data.updateBanner.brand_id'));
        $this->assertEquals($banner->product_id, $response->json('data.updateBanner.product_id'));
        $this->assertEquals($banner->created_at->timestamp, $response->json('data.updateBanner.created_at'));
        $this->assertEquals($banner->updated_at->timestamp, $response->json('data.updateBanner.updated_at'));
        $this->assertEquals($banner->deleted_at?->timestamp, $response->json('data.updateBanner.deleted_at'));
    }
}
