<?php

namespace Tests\GraphQL\Installment\Mutations;

use App\Enums\Company\AttachmentTypeEnum;
use App\Enums\Company\Rbac\PermissionEnum;
use App\Models\Company\Attachment;
use App\Models\Company\Branch;
use App\Models\Company\CompanyAdmin;
use App\Models\Company\Installment;
use App\Models\Company\Rbac\Permission;
use App\Models\Company\Rbac\Role;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
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
        $branch = Branch::query()->firstOrFail();
        $this->_installment = Installment::factory()->create(['branch_id' => $branch->id]);
    }

    #[Test]
    public function successUploadDocument()
    {
        $response = $this->sendRequest();
        $this->assertSuccessResponse($response);
    }

    #[Test]
    public function errorWithInvalidInstallmentId()
    {
        $response = $this->sendRequest(installmentId: 999);

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
        $response = $this->sendRequest(name: Str::random(65));

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
        $response = $this->sendRequest(file: UploadedFile::fake()->create('original_test.jpg', mimeType: 'application/zip'));

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
        $response = $this->sendRequest(file: UploadedFile::fake()->create('original_test.jpg', kilobytes: 10241, mimeType: 'image/jpeg'));

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

    #[Test]
    public function successCheckForPermission()
    {
        $role = Role::factory()->create();
        $simpleAdmin = $this->createSimpleAdminUser($role->id);
        $permission = Permission::query()->where('slug', PermissionEnum::INSTALLMENT_UPLOAD_DOCUMENT)->firstOrFail();

        $branches = Branch::factory()->count(2)->create();
        $this->_installment = Installment::factory()->create(['branch_id' => $branches[1]->id]);

        $role->permissions()->attach($permission, ['branch_id' => $branches[0]->id]);
        $response = $this->sendRequest(admin: $simpleAdmin);
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'installment id']), $response->json('errors.0.extensions.validation.installment_id.0'));

        $role->permissions()->attach($permission, ['branch_id' => $branches[1]->id]);
        $response = $this->sendRequest(admin: $simpleAdmin);
        $this->assertSuccessResponse($response);
    }

    private function sendRequest(?CompanyAdmin $admin = null, ?int $installmentId = null, ?string $name = null, $file = null): TestResponse
    {
        return $this
            ->actingAs($admin ?? $this->createAdminUser())
            ->call(
                'POST',
                route('graphql.installment'),
                $this->buildGraphQLQuery($installmentId ?? $this->_installment->id, $name ?? 'test.jpg'),
                [],
                ['0' => $file ?? UploadedFile::fake()->image('original_test.jpg')],
                [
                    'CONTENT_TYPE' => 'multipart/form-data',
                ],
            );
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

    private function assertSuccessResponse(TestResponse $response): void
    {
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
}
