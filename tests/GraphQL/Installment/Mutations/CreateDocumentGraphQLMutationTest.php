<?php

namespace Tests\GraphQL\Installment\Mutations;

use App\Enums\Company\AttachmentTypeEnum;
use App\Models\Company\Attachment;
use App\Models\Company\Installment;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class CreateDocumentGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private Installment $_installment;

    protected function setUp(): void
    {
        parent::setUp();

        Attachment::query()->forceDelete();
        Installment::query()->forceDelete();
        $this->_installment = Installment::factory()->create();
    }

    #[Test]
    public function successUploadDocument()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->call(
                'POST',
                route('graphql.installment'),
                $this->buildGraphQLQuery(installment_id: $this->_installment->id, name: 'test.jpg'),
                [],
                ['0' => UploadedFile::fake()->image('original_test.jpg')],
                [
                    'CONTENT_TYPE' => 'multipart/form-data',
                ],
            );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'createDocument' => [
                    'id',
                    'type',
                    'url',
                    'name',
                    'original_name',
                    'mime',
                    'created_at',
                    'updated_at',
                    'deleted_at',
                ],
            ],
        ]);
        $attachment = Attachment::query()->find($response->json('data.createDocument.id'));
        $this->assertNotNull($attachment);
        $this->assertEquals(AttachmentTypeEnum::INSTALLMENT_DOCUMENT, $attachment->type);
        $this->assertEquals('test.jpg', $attachment->name);
        $this->assertEquals('image/jpeg', $attachment->mime);
        $this->assertNotNull($attachment->url);

        $this->assertEquals($attachment->name, $response->json('data.createDocument.name'));
        $this->assertEquals($attachment->original_name, $response->json('data.createDocument.original_name'));
        $this->assertEquals($attachment->mime, $response->json('data.createDocument.mime'));
        $this->assertEquals($attachment->created_at->timestamp, $response->json('data.createDocument.created_at'));
        $this->assertEquals($attachment->updated_at->timestamp, $response->json('data.createDocument.updated_at'));
        $this->assertNull($response->json('data.createDocument.deleted_at'));

        $this->assertEquals($this->_installment->id, $attachment->attachmentable_id);
        $this->assertEquals(Installment::class, $attachment->attachmentable_type);
    }

    #[Test]
    public function errorWithInvalidInstallmentId()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->call(
                'POST',
                route('graphql.installment'),
                $this->buildGraphQLQuery(installment_id: 999, name: 'test.jpg'),
                [],
                ['0' => UploadedFile::fake()->image('original_test.jpg')],
                [
                    'CONTENT_TYPE' => 'multipart/form-data',
                ],
            );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'errors' => [
                [
                    'message',
                    'extensions' => [
                        'validation' => [
                            'installment_id',
                        ],
                    ],
                ],
            ],
        ]);
        $this->assertEquals(trans('validation.exists', ['attribute' => 'installment id']), $response->json('errors.0.extensions.validation.installment_id.0'));
    }

    #[Test]
    public function errorLongName()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->call(
                'POST',
                route('graphql.installment'),
                $this->buildGraphQLQuery(installment_id: $this->_installment->id, name: Str::random(65)),
                [],
                ['0' => UploadedFile::fake()->image('original_test.jpg')],
                [
                    'CONTENT_TYPE' => 'multipart/form-data',
                ],
            );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'errors' => [
                [
                    'message',
                    'extensions' => [
                        'validation' => [
                            'name',
                        ],
                    ],
                ],
            ],
        ]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => trans('validation.attributes.name'), 'max' => 64]), $response->json('errors.0.extensions.validation.name.0'));
    }

    #[Test]
    public function errorInvalidFileType()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->call(
                'POST',
                route('graphql.installment'),
                $this->buildGraphQLQuery(installment_id: $this->_installment->id, name: 'test'),
                [],
                ['0' => UploadedFile::fake()->create('original_test.jpg', mimeType: 'application/zip')],
                [
                    'CONTENT_TYPE' => 'multipart/form-data',
                ],
            );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'errors' => [
                [
                    'message',
                    'extensions' => [
                        'validation' => [
                            'file',
                        ],
                    ],
                ],
            ],
        ]);
        $this->assertEquals(trans('validation.mimes', ['attribute' => 'file', 'values' => 'docx, doc, pdf, png, jpg, jpeg']), $response->json('errors.0.extensions.validation.file.0'));
    }

    #[Test]
    public function errorBigSizeFile()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->call(
                'POST',
                route('graphql.installment'),
                $this->buildGraphQLQuery(installment_id: $this->_installment->id, name: 'test'),
                [],
                ['0' => UploadedFile::fake()->create('original_test.jpg', kilobytes: 10241, mimeType: 'image/jpeg')],
                [
                    'CONTENT_TYPE' => 'multipart/form-data',
                ],
            );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'errors' => [
                [
                    'message',
                    'extensions' => [
                        'validation' => [
                            'file',
                        ],
                    ],
                ],
            ],
        ]);
        $this->assertEquals(trans('validation.max.file', ['attribute' => 'file', 'max' => 10240]), $response->json('errors.0.extensions.validation.file.0'));
    }

    private function buildGraphQLQuery(int $installment_id, string $name): array
    {
        return [
            'operations' => json_encode([
                'query' => 'mutation UploadDocument($installment_id: Int!, $name: String!, $file: Upload!) {
                    createDocument(file: $file, installment_id: $installment_id, name: $name) {
                        id
                        type
                        url
                        name
                        original_name
                        mime
                        created_at
                        updated_at
                        deleted_at
                    }
                }',
                'variables' => [
                    'file' => null,
                    'name' => $name,
                    'installment_id' => $installment_id,
                ],
            ]),
            'map' => json_encode([
                '0' => ['variables.file'],
            ]),
        ];
    }
}
