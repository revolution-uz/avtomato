<?php

namespace Tests\GraphQL\Management\Mutations;

use App\Enums\Company\AttachmentTypeEnum;
use App\Models\Company\Attachment;
use App\Models\Company\CompanyAdmin;
use Illuminate\Http\UploadedFile;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class ChangeProfileAvatarGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private CompanyAdmin $_admin;

    private array $_structure = [
        'id',
        'type',
        'url',
        'name',
        'original_name',
        'mime',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        Attachment::query()->forceDelete();
        CompanyAdmin::query()->forceDelete();
        $this->_admin = $this->createAdminUser();
    }

    #[Test]
    public function successUploadAvatar()
    {
        $response = $this
            ->actingAs($this->_admin)
            ->call(
                'POST',
                route('graphql.management'),
                $this->buildGraphQLQuery(),
                [],
                ['0' => UploadedFile::fake()->image('original_test.jpg')],
                [
                    'CONTENT_TYPE' => 'multipart/form-data',
                ],
            );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'changeProfileAvatar' => $this->_structure,
            ],
        ]);

        $attachment = Attachment::query()->find($response->json('data.changeProfileAvatar.id'));
        $this->assertNotNull($attachment);
        $this->assertEquals(AttachmentTypeEnum::AVATAR, $attachment->type);
        $this->assertNotNull($attachment->url);
        $this->assertEquals($attachment->name, $response->json('data.changeProfileAvatar.name'));
        $this->assertEquals($attachment->original_name, $response->json('data.changeProfileAvatar.original_name'));
        $this->assertEquals('image/jpeg', $attachment->mime);
        $this->assertEquals($attachment->mime, $response->json('data.changeProfileAvatar.mime'));
        $this->assertEquals($attachment->created_at->timestamp, $response->json('data.changeProfileAvatar.created_at'));
        $this->assertEquals($attachment->updated_at->timestamp, $response->json('data.changeProfileAvatar.updated_at'));
        $this->assertNull($response->json('data.changeProfileAvatar.deleted_at'));
        $this->assertEquals($this->_admin->id, $attachment->attachmentable_id);
        $this->assertEquals(CompanyAdmin::class, $attachment->attachmentable_type);
    }

    #[Test]
    public function errorInvalidFileType()
    {
        $response = $this
            ->actingAs($this->_admin)
            ->call(
                'POST',
                route('graphql.management'),
                $this->buildGraphQLQuery(),
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
                            'avatar',
                        ],
                    ],
                ],
            ],
        ]);
        $this->assertEquals(trans('validation.mimes', ['attribute' => 'Аватар', 'values' => 'jpeg, png, jpg']), $response->json('errors.0.extensions.validation.avatar.0'));
    }

    #[Test]
    public function errorBigSizeFile()
    {
        $response = $this
            ->actingAs($this->_admin)
            ->call(
                'POST',
                route('graphql.management'),
                $this->buildGraphQLQuery(),
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
                            'avatar',
                        ],
                    ],
                ],
            ],
        ]);
        $this->assertEquals(trans('validation.max.file', ['attribute' => 'Аватар', 'max' => 10240]), $response->json('errors.0.extensions.validation.avatar.0'));
    }

    private function buildGraphQLQuery(): array
    {
        return [
            'operations' => json_encode([
                'query' => 'mutation ChangeProfileAvatar($avatar: Upload!) {
                    changeProfileAvatar(avatar: $avatar) {' . $this->convertIntoQuery($this->_structure) . '}
                }',
                'variables' => [
                    'avatar' => null,
                ],
            ]),
            'map' => json_encode([
                '0' => ['variables.avatar'],
            ]),
        ];
    }

    private function convertIntoQuery(array $structure): string
    {
        foreach ($structure as $key => $value) {
            if (is_array($value)) {
                if ($key == '*') {
                    $structure[$key] = $this->convertIntoQuery($value);
                } else {
                    $structure[$key] = "{$key} {{$this->convertIntoQuery($value)}}";
                }
            }
        }

        return implode(' ', $structure);
    }
}
