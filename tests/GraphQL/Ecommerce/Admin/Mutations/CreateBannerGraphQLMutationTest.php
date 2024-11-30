<?php

namespace Tests\GraphQL\Ecommerce\Admin\Mutations;

use App\Models\Company\CompanyAdmin;
use App\Models\Company\Ecommerce\Banner;
use App\Models\Company\Ecommerce\Image;
use App\Models\Company\Ecommerce\Product;
use App\Models\Company\Ecommerce\ProductBrand;
use App\Models\Company\Ecommerce\ProductCategory;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class CreateBannerGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private CompanyAdmin $_admin;

    private array $_structure = [
        'id',
        'name_uz',
        'name_ru',
        'name_en',
        'name_oz',
        'image_uz_url',
        'image_ru_url',
        'image_en_url',
        'image_oz_url',
        'is_active',
        'url',
        'category_id',
        'brand_id',
        'product_id',
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
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake();

        Image::query()->forceDelete();
        $this->_admin = $this->createAdminUser();
    }

    #[Test]
    public function successCreateBanner()
    {
        $category = ProductCategory::factory()->create();
        $brand = ProductBrand::factory()->create();
        $product = Product::factory()->create();

        $variables = [
            'name_uz' => Str::random(255),
            'name_ru' => Str::random(255),
            'name_en' => Str::random(255),
            'name_oz' => Str::random(255),
            'url' => Str::random(255),
            'is_active' => (bool) random_int(0, 1),
            'category_id' => $category->id,
            'brand_id' => $brand->id,
            'product_id' => $product->id,
        ];

        $files = [
            'image_uz' => UploadedFile::fake()->image('original_test.jpg'),
            'image_ru' => UploadedFile::fake()->image('original_test.jpg'),
            'image_en' => UploadedFile::fake()->image('original_test.jpg'),
            'image_oz' => UploadedFile::fake()->image('original_test.jpg'),
        ];

        $response = $this->sendRequest($variables, $files);

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'createBanner' => $this->_structure,
            ],
        ]);

        $banner = Banner::query()->with('image')->findOrFail($response->json('data.createBanner.id'));

        $this->assertEquals($banner->name_uz, $variables['name_uz']);
        $this->assertEquals($banner->name_ru, $variables['name_ru']);
        $this->assertEquals($banner->name_en, $variables['name_en']);
        $this->assertEquals($banner->name_oz, $variables['name_oz']);
        $this->assertEquals($banner->is_active, $variables['is_active']);
        $this->assertEquals($banner->url, $variables['url']);
        $this->assertEquals($banner->category_id, $variables['category_id']);
        $this->assertEquals($banner->brand_id, $variables['brand_id']);
        $this->assertEquals($banner->product_id, $variables['product_id']);
        $this->assertEquals($banner->name_uz, $response->json('data.createBanner.name_uz'));
        $this->assertEquals($banner->name_ru, $response->json('data.createBanner.name_ru'));
        $this->assertEquals($banner->name_en, $response->json('data.createBanner.name_en'));
        $this->assertEquals($banner->name_oz, $response->json('data.createBanner.name_oz'));
        $this->assertEquals($banner->is_active, $response->json('data.createBanner.is_active'));
        $this->assertEquals($banner->url, $response->json('data.createBanner.url'));
        $this->assertEquals($banner->category_id, $response->json('data.createBanner.category_id'));
        $this->assertEquals($banner->brand_id, $response->json('data.createBanner.brand_id'));
        $this->assertEquals($banner->product_id, $response->json('data.createBanner.product_id'));
        $this->assertEquals($banner->created_at->timestamp, $response->json('data.createBanner.created_at'));
        $this->assertEquals($banner->updated_at->timestamp, $response->json('data.createBanner.updated_at'));
        $this->assertEquals($banner->deleted_at?->timestamp, $response->json('data.createBanner.deleted_at'));

        $image = $banner->image;
        Storage::disk('public')->assertExists(Banner::UPLOADS_PATH . '/uz/' . $image->path);
        Storage::disk('public')->assertExists(Banner::UPLOADS_PATH . '/ru/' . $image->path);
        Storage::disk('public')->assertExists(Banner::UPLOADS_PATH . '/en/' . $image->path);
        Storage::disk('public')->assertExists(Banner::UPLOADS_PATH . '/oz/' . $image->path);
        $this->assertEquals('image/jpeg', $image->mime);
        $this->assertEquals(asset(Banner::UPLOADS_PATH . '/uz/' . $image->path), $response->json('data.createBanner.image_uz_url'));
        $this->assertEquals(asset(Banner::UPLOADS_PATH . '/ru/' . $image->path), $response->json('data.createBanner.image_ru_url'));
        $this->assertEquals(asset(Banner::UPLOADS_PATH . '/en/' . $image->path), $response->json('data.createBanner.image_en_url'));
        $this->assertEquals(asset(Banner::UPLOADS_PATH . '/oz/' . $image->path), $response->json('data.createBanner.image_oz_url'));
    }

    #[Test]
    public function successCreateBannerWithOnlyRequiredAttributes()
    {
        $variables = [
            'name_uz' => Str::random(255),
            'name_ru' => Str::random(255),
            'name_en' => Str::random(255),
            'name_oz' => Str::random(255),
            'url' => Str::random(255),
            'is_active' => (bool) random_int(0, 1),
        ];

        $files = [
            'image_uz' => UploadedFile::fake()->image('original_test.jpg'),
        ];

        $response = $this->sendRequest($variables, $files);

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'createBanner' => Arr::except($this->_structure, ['category', 'brand', 'product']),
            ],
        ]);

        $banner = Banner::query()->with('image')->findOrFail($response->json('data.createBanner.id'));

        $this->assertEquals($banner->name_uz, $variables['name_uz']);
        $this->assertEquals($banner->name_ru, $variables['name_ru']);
        $this->assertEquals($banner->name_en, $variables['name_en']);
        $this->assertEquals($banner->name_oz, $variables['name_oz']);
        $this->assertEquals($banner->is_active, $variables['is_active']);
        $this->assertEquals($banner->url, $variables['url']);
        $this->assertNull($banner->category_id);
        $this->assertNull($banner->brand_id);
        $this->assertNull($banner->product_id);
        $this->assertEquals($banner->name_uz, $response->json('data.createBanner.name_uz'));
        $this->assertEquals($banner->name_ru, $response->json('data.createBanner.name_ru'));
        $this->assertEquals($banner->name_en, $response->json('data.createBanner.name_en'));
        $this->assertEquals($banner->name_oz, $response->json('data.createBanner.name_oz'));
        $this->assertEquals($banner->is_active, $response->json('data.createBanner.is_active'));
        $this->assertEquals($banner->url, $response->json('data.createBanner.url'));
        $this->assertNull($response->json('data.createBanner.category_id'));
        $this->assertNull($response->json('data.createBanner.brand_id'));
        $this->assertNull($response->json('data.createBanner.product_id'));
        $this->assertEquals($banner->created_at->timestamp, $response->json('data.createBanner.created_at'));
        $this->assertEquals($banner->updated_at->timestamp, $response->json('data.createBanner.updated_at'));
        $this->assertEquals($banner->deleted_at?->timestamp, $response->json('data.createBanner.deleted_at'));

        $image = $banner->image;
        Storage::disk('public')->assertExists(Banner::UPLOADS_PATH . '/uz/' . $image->path);
        Storage::disk('public')->assertMissing(Banner::UPLOADS_PATH . '/ru/' . $image->path);
        Storage::disk('public')->assertMissing(Banner::UPLOADS_PATH . '/en/' . $image->path);
        Storage::disk('public')->assertMissing(Banner::UPLOADS_PATH . '/oz/' . $image->path);
        $this->assertEquals('image/jpeg', $image->mime);
        $this->assertEquals(asset(Banner::UPLOADS_PATH . '/uz/' . $image->path), $response->json('data.createBanner.image_uz_url'));
        $this->assertEquals(asset(Banner::UPLOADS_PATH . '/uz/' . $image->path), $response->json('data.createBanner.image_ru_url'));
        $this->assertEquals(asset(Banner::UPLOADS_PATH . '/uz/' . $image->path), $response->json('data.createBanner.image_en_url'));
        $this->assertEquals(asset(Banner::UPLOADS_PATH . '/uz/' . $image->path), $response->json('data.createBanner.image_oz_url'));
    }

    #[Test]
    public function errorValidation(): void
    {
        // GraphQL type errors
        $response = $this->sendRequest(['category_id' => null, 'brand_id' => null, 'product_id' => null]);

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$name_uz" of required type "String!" was not provided.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$name_ru" of required type "String!" was not provided.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$name_en" of required type "String!" was not provided.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$name_oz" of required type "String!" was not provided.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$image_uz" of non-null type "Upload!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$url" of required type "String!" was not provided.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$is_active" of required type "Boolean!" was not provided.'));

        // rule errors
        $response = $this->sendRequest([
            'name_uz' => '',
            'name_ru' => '',
            'name_en' => '',
            'name_oz' => '',
            'url' => '',
            'is_active' => true,
            'category_id' => 999999999,
            'brand_id' => 999999999,
            'product_id' => 999999999,
        ], [
            'image_uz' => UploadedFile::fake()->image('original_test.jpg'),
        ]);

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.required', ['attribute' => 'banner.name uz']), $response->json('errors.0.extensions.validation')['banner.name_uz'][0]);
        $this->assertEquals(trans('validation.required', ['attribute' => 'banner.name ru']), $response->json('errors.0.extensions.validation')['banner.name_ru'][0]);
        $this->assertEquals(trans('validation.required', ['attribute' => 'banner.name en']), $response->json('errors.0.extensions.validation')['banner.name_en'][0]);
        $this->assertEquals(trans('validation.required', ['attribute' => 'banner.name oz']), $response->json('errors.0.extensions.validation')['banner.name_oz'][0]);
        $this->assertEquals(trans('validation.required', ['attribute' => 'banner.url']), $response->json('errors.0.extensions.validation')['banner.url'][0]);
        $this->assertEquals(trans('validation.exists', ['attribute' => 'banner.category id']), $response->json('errors.0.extensions.validation')['banner.category_id'][0]);
        $this->assertEquals(trans('validation.exists', ['attribute' => 'banner.brand id']), $response->json('errors.0.extensions.validation')['banner.brand_id'][0]);
        $this->assertEquals(trans('validation.exists', ['attribute' => 'banner.product id']), $response->json('errors.0.extensions.validation')['banner.product_id'][0]);
    }

    #[Test]
    public function errorInvalidFileType()
    {
        $response = $this->sendRequest([
            'name_uz' => Str::random(255),
            'name_ru' => Str::random(255),
            'name_en' => Str::random(255),
            'name_oz' => Str::random(255),
            'url' => Str::random(255),
            'is_active' => (bool) random_int(0, 1),
        ], [
            'image_uz' => UploadedFile::fake()->create('original_test.jpg', mimeType: 'application/zip'),
        ]);

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals(trans('validation.mimes', ['attribute' => 'banner.image uz', 'values' => 'jpg, jpeg, png']), $response->json('errors.0.extensions.validation')['banner.image_uz'][0]);
    }

    #[Test]
    public function errorBigSizeFile()
    {
        $response = $this->sendRequest([
            'name_uz' => Str::random(255),
            'name_ru' => Str::random(255),
            'name_en' => Str::random(255),
            'name_oz' => Str::random(255),
            'url' => Str::random(255),
            'is_active' => (bool) random_int(0, 1),
        ], [
            'image_uz' => UploadedFile::fake()->create('original_test.jpg', kilobytes: 1025, mimeType: 'image/jpeg'),
        ]);

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals(trans('validation.max.file', ['attribute' => 'banner.image uz', 'max' => 1024]), $response->json('errors.0.extensions.validation')['banner.image_uz'][0]);
    }

    private function sendRequest(array $variables = [], array $files = [])
    {
        return $this
            ->actingAs($this->_admin)
            ->call(
                'POST',
                route('graphql.ecommerce-admin'),
                $this->buildGraphQLQuery($variables),
                [],
                $files,
                ['CONTENT_TYPE' => 'multipart/form-data'],
            );
    }

    private function buildGraphQLQuery(array $variables): array
    {
        return [
            'operations' => json_encode([
                'query' => 'mutation CreateBanner(
                    $category_id: Int
                    $brand_id: Int
                    $product_id: Int
                    $name_uz: String!
                    $name_ru: String!
                    $name_en: String!
                    $name_oz: String!
                    $image_uz: Upload!
                    $image_ru: Upload
                    $image_en: Upload
                    $image_oz: Upload
                    $url: String!
                    $is_active: Boolean!
                ) {
                    createBanner(
                        banner: {
                            category_id: $category_id
                            brand_id: $brand_id
                            product_id: $product_id
                            name_uz: $name_uz
                            name_ru: $name_ru
                            name_en: $name_en
                            name_oz: $name_oz
                            image_uz: $image_uz
                            image_ru: $image_ru
                            image_en: $image_en
                            image_oz: $image_oz
                            url: $url
                            is_active: $is_active
                        }
                    ) {' . $this->convertIntoQuery($this->_structure) . '}
                }',
                'variables' => array_merge($variables, ['image' => null]),
            ]),
            'map' => json_encode([
                'image_uz' => ['variables.image_uz'],
                'image_ru' => ['variables.image_ru'],
                'image_en' => ['variables.image_en'],
                'image_oz' => ['variables.image_oz'],
            ]),
        ];
    }
}
