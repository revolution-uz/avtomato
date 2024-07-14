<?php

namespace Management\Mutations;

use App\Models\Company\Attachment;
use App\Models\Company\CompanyAdmin;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class UpdateAdminGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private CompanyAdmin $_adminInput;

    private array $_structure = [
        'id',
        'username',
        'first_name',
        'last_name',
        'phone',
        'status',
        'language',
        'role_id',
        'created_at',
        'updated_at',
        'deleted_at',
        'avatar' => [
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
        'template_theme' => [
            'layout_name',
        ],
        'role' => [
            'id',
            'name',
            'created_at',
            'updated_at',
            'deleted_at',
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->_adminInput = CompanyAdmin::factory()->create();
        Attachment::factory()->create([
            'attachmentable_id' => $this->_adminInput->id,
            'attachmentable_type' => get_class($this->_adminInput),
        ]);
    }

    #[Test]
    public function successWithoutArguments()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery([
                    'id' => $this->_adminInput->id,
                ])
            );

        $this->assertSuccessUpdateAdminResponse($response);
    }

    #[Test]
    public function successWithArguments()
    {
        $input = [
            'id' => $this->_adminInput->id,
            'first_name' => Str::random(50),
            'last_name' => Str::random(50),
        ];

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery($input)
            );

        $this->assertSuccessUpdateAdminResponse($response, $input);
    }

    #[Test]
    public function errorArgumentsValidation()
    {
        $input = [
            'id' => null,
            'first_name' => null,
            'last_name' => null,
        ];
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery($input)
            );
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('Variable "$id" of non-null type "Int!" must not be null.', $response->json('errors.0.message'));

        $input = [
            'id' => $this->_adminInput->id,
            'first_name' => Str::random(51),
            'last_name' => Str::random(51),
        ];
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery($input)
            );
        $response->assertJsonStructure([
            'errors' => [
                [
                    'message',
                    'extensions' => [
                        'validation' => [
                            'first_name',
                            'last_name',
                        ],
                    ],
                ],
            ],
        ]);
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'first name', 'max' => 50]), $response->json('errors.0.extensions.validation.first_name.0'));
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'last name', 'max' => 50]), $response->json('errors.0.extensions.validation.last_name.0'));

        $input = [
            'id' => CompanyAdmin::query()->orderByDesc('id')->first()->id + 1,
            'first_name' => Str::random(50),
            'last_name' => Str::random(50),
        ];
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery($input)
            );
        $response->assertJsonStructure([
            'errors' => [
                [
                    'message',
                    'extensions' => [
                        'validation' => [
                            'id',
                        ],
                    ],
                ],
            ],
        ]);
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'id']), $response->json('errors.0.extensions.validation.id.0'));
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $query['query'] = '
            mutation UpdateAdmin($id: Int!, $first_name: String, $last_name: String) {
                updateAdmin(id: $id, first_name: $first_name, last_name: $last_name) {' . $this->convertIntoQuery($this->_structure) . '}
            }
        ';

        return $query;
    }

    private function assertSuccessUpdateAdminResponse(TestResponse $response, ?array $input = null)
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'updateAdmin' => $this->_structure,
            ],
        ]);

        $this->_adminInput->refresh();
        $this->assertEquals($this->_adminInput->id, $response->json('data.updateAdmin.id'));
        $this->assertEquals($this->_adminInput->username, $response->json('data.updateAdmin.username'));
        $this->assertEquals($this->_adminInput->first_name, $response->json('data.updateAdmin.first_name'));
        $this->assertEquals($this->_adminInput->last_name, $response->json('data.updateAdmin.last_name'));
        $this->assertEquals($this->_adminInput->phone, $response->json('data.updateAdmin.phone'));
        $this->assertEquals($this->_adminInput->status->value, $response->json('data.updateAdmin.status'));
        $this->assertEquals($this->_adminInput->language, $response->json('data.updateAdmin.language'));
        $this->assertEquals($this->_adminInput->role_id, $response->json('data.updateAdmin.role_id'));
        $this->assertEquals($this->_adminInput->created_at->timestamp, $response->json('data.updateAdmin.created_at'));
        $this->assertEquals($this->_adminInput->updated_at->timestamp, $response->json('data.updateAdmin.updated_at'));
        $this->assertNull($response->json('data.updateAdmin.deleted_at'));

        $this->assertEquals($this->_adminInput->avatar->id, $response->json('data.updateAdmin.avatar.id'));
        $this->assertEquals($this->_adminInput->avatar->type->value, $response->json('data.updateAdmin.avatar.type'));
        $this->assertEquals($this->_adminInput->avatar->url, $response->json('data.updateAdmin.avatar.url'));
        $this->assertEquals($this->_adminInput->avatar->name, $response->json('data.updateAdmin.avatar.name'));
        $this->assertEquals($this->_adminInput->avatar->original_name, $response->json('data.updateAdmin.avatar.original_name'));
        $this->assertEquals($this->_adminInput->avatar->mime, $response->json('data.updateAdmin.avatar.mime'));
        $this->assertEquals($this->_adminInput->avatar->created_at->timestamp, $response->json('data.updateAdmin.avatar.created_at'));
        $this->assertEquals($this->_adminInput->avatar->updated_at->timestamp, $response->json('data.updateAdmin.avatar.updated_at'));
        $this->assertEquals($this->_adminInput->avatar->deleted_at?->timestamp, $response->json('data.updateAdmin.avatar.deleted_at'));

        $this->assertEquals($this->_adminInput->template_theme->layout_name, $response->json('data.updateAdmin.template_theme.layout_name'));

        $this->assertEquals($this->_adminInput->role->id, $response->json('data.updateAdmin.role.id'));
        $this->assertEquals($this->_adminInput->role->name, $response->json('data.updateAdmin.role.name'));
        $this->assertEquals($this->_adminInput->role->created_at->timestamp, $response->json('data.updateAdmin.role.created_at'));
        $this->assertEquals($this->_adminInput->role->updated_at->timestamp, $response->json('data.updateAdmin.role.updated_at'));
        $this->assertEquals($this->_adminInput->role->deleted_at?->timestamp, $response->json('data.updateAdmin.role.deleted_at'));

        if ($input !== null) {
            $this->assertEquals($input['first_name'], $this->_adminInput->first_name);
            $this->assertEquals($input['last_name'], $this->_adminInput->last_name);
            $this->assertEquals($this->_adminInput->first_name, $response->json('data.updateAdmin.first_name'));
            $this->assertEquals($this->_adminInput->last_name, $response->json('data.updateAdmin.last_name'));
        }
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
