<?php

namespace Tests\GraphQL\Ecommerce\Admin\Mutations;

use App\Enums\Company\CompanyStatusEnum;
use App\Models\Company\Ecommerce\EcommerceImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class UploadEcommerceImageGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private array $_structure = [
        'id',
        'url',
        'original_name',
        'mime',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
        EcommerceImage::query()->forceDelete();
    }

    #[Test]
    public function successUploadImageProductCategory()
    {
        $response = $this->sendRequest(UploadedFile::fake()->image('original_test.jpg'));

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'uploadEcommerceImage' => $this->_structure,
            ],
        ]);

        $ecommerceImage = EcommerceImage::query()->firstOrFail();

        $this->assertEquals($ecommerceImage->id, $response->json('data.uploadEcommerceImage.id'));
        $this->assertEquals($ecommerceImage->url, $response->json('data.uploadEcommerceImage.url'));
        $this->assertEquals($ecommerceImage->original_name, $response->json('data.uploadEcommerceImage.original_name'));
        $this->assertEquals($ecommerceImage->mime, $response->json('data.uploadEcommerceImage.mime'));
        $this->assertEquals($ecommerceImage->created_at->timestamp, $response->json('data.uploadEcommerceImage.created_at'));
        $this->assertEquals($ecommerceImage->updated_at->timestamp, $response->json('data.uploadEcommerceImage.updated_at'));
        $this->assertEquals($ecommerceImage->deleted_at?->timestamp, $response->json('data.uploadEcommerceImage.deleted_at'));
        $this->assertEquals(asset(EcommerceImage::UPLOADS_PATH . '/' . $ecommerceImage->path), $response->json('data.uploadEcommerceImage.url'));
        Storage::disk('public')->assertExists(EcommerceImage::UPLOADS_PATH . '/' . $ecommerceImage->path);
        $this->assertEquals('image/jpeg', $ecommerceImage->mime);
    }

    #[Test]
    public function errorValidation()
    {
        // Error invalid file type
        $response = $this->sendRequest(UploadedFile::fake()->create('original_test.jpg', mimeType: 'application/zip'));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals(trans('validation.mimes', ['attribute' => 'image', 'values' => 'jpeg, png, jpg']), $response->json('errors.0.extensions.validation.image.0'));

        // Error big size file
        $response = $this->sendRequest(UploadedFile::fake()->create('original_test.jpg', kilobytes: 1025, mimeType: 'image/jpeg'));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals(trans('validation.max.file', ['attribute' => 'image', 'max' => 1024]), $response->json('errors.0.extensions.validation.image.0'));
    }

    #[Test]
    public function errorCreateCustomerWithInactiveCompany(): void
    {
        tenant()->updateOrFail(['status' => CompanyStatusEnum::INACTIVE]);

        $response = $this->sendRequest(UploadedFile::fake()->image('original_test.jpg'));

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals(__('api.inactive_company'), $response->json('errors.0.message'));
    }

    private function sendRequest(?UploadedFile $image)
    {
        return $this->actingAs($this->createAdminUser())
            ->call(
                'POST',
                route('graphql.ecommerce-admin'),
                $this->buildGraphQLQuery(),
                [],
                ['image' => $image],
                ['CONTENT_TYPE' => 'multipart/form-data'],
            );
    }

    private function buildGraphQLQuery(): array
    {
        return [
            'operations' => json_encode([
                'query' => <<<GRAPHQL
                    mutation UploadEcommerceImage(\$image: Upload) {
                        uploadEcommerceImage(image: \$image) {
                            {$this->convertIntoQuery($this->_structure)}
                        }
                    }
                GRAPHQL,
                'variables' => [],
            ]),
            'map' => json_encode([
                'image' => ['variables.image'],
            ]),
        ];
    }
}
