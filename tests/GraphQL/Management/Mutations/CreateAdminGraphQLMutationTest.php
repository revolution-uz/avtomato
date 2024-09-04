<?php

namespace Tests\GraphQL\Management\Mutations;

use App\Enums\Company\CompanyStatusEnum;
use App\Enums\Company\UserStatusEnum;
use App\Models\Company\CompanyAdmin;
use App\Models\Company\Rbac\Role;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class CreateAdminGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private array $_input;

    protected function setUp(): void
    {
        parent::setUp();

        CompanyAdmin::factory()->count(5)->create();

        $this->_input = [
            'username' => Str::random(50),
            'first_name' => Str::random(50),
            'last_name' => Str::random(50),
            'role_id' => Role::query()->inRandomOrder()->first()->id,
            'status' => (int) UserStatusEnum::randomValue(),
            'password' => 'password',
            'password_confirmation' => 'password',
        ];
    }

    #[Test]
    public function successCreateAdmin()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.management'), $this->buildGraphQLQuery(['admin' => $this->_input]));

        $this->assertsSuccessResponse($response);
    }

    #[Test]
    public function errorUnique()
    {
        $this->_input = array_merge($this->_input, ['username' => CompanyAdmin::query()->inRandomOrder()->first()->username]);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.management'), $this->buildGraphQLQuery(['admin' => $this->_input]));

        $response->assertOk();
        $response->assertJsonStructure([
            'errors' => [
                [
                    'message',
                    'extensions' => [
                        'validation' => [
                            'admin.username',
                        ],
                    ],
                ],
            ],
        ]);
        $this->assertEquals(trans('validation.unique', ['attribute' => 'admin.username']), $response->json('errors.0.extensions.validation')['admin.username'][0]);
    }

    #[Test]
    public function errorArgumentsValidation()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['admin' => [
                    'username' => null,
                    'first_name' => null,
                    'last_name' => null,
                    'role_id' => null,
                    'status' => null,
                    'password' => null,
                    'password_confirmation' => null,
                ]])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$admin" got invalid value null at "admin.username"; Expected non-nullable type "String!" not to be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$admin" got invalid value null at "admin.first_name"; Expected non-nullable type "String!" not to be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$admin" got invalid value null at "admin.last_name"; Expected non-nullable type "String!" not to be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$admin" got invalid value null at "admin.role_id"; Expected non-nullable type "Int!" not to be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$admin" got invalid value null at "admin.status"; Expected non-nullable type "Int!" not to be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$admin" got invalid value null at "admin.password"; Expected non-nullable type "String!" not to be null.'));

        $password = Str::random(129);
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['admin' => [
                    'username' => Str::random(51),
                    'first_name' => Str::random(51),
                    'last_name' => Str::random(51),
                    'role_id' => Role::query()->orderByDesc('id')->first()->id + 1,
                    'status' => rand(100, 999),
                    'password' => $password,
                    'password_confirmation' => $password,
                ]])
            );
        $response->assertOk();
        $response->assertJsonStructure([
            'errors' => [
                [
                    'message',
                    'extensions' => [
                        'validation' => [
                            'admin.first_name',
                            'admin.last_name',
                            'admin.role_id',
                            'admin.status',
                            'admin.password',
                        ],
                    ],
                ],
            ],
        ]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'admin.first name', 'max' => 50]), $response->json('errors.0.extensions.validation')['admin.first_name'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'admin.last name', 'max' => 50]), $response->json('errors.0.extensions.validation')['admin.last_name'][0]);
        $this->assertEquals(trans('validation.exists', ['attribute' => 'admin.role id']), $response->json('errors.0.extensions.validation')['admin.role_id'][0]);
        $this->assertEquals(trans('validation.in', ['attribute' => 'admin.status']), $response->json('errors.0.extensions.validation')['admin.status'][0]);
        $this->assertEquals(trans('validation.max.string', ['attribute' => 'admin.password', 'max' => 128]), $response->json('errors.0.extensions.validation')['admin.password'][0]);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['admin' => array_merge($this->_input, [
                    'password' => Str::random(50),
                    'password_confirmation' => Str::random(50),
                ])])
            );
        $response->assertOk();
        $response->assertJsonStructure([
            'errors' => [
                [
                    'message',
                    'extensions' => [
                        'validation' => [
                            'admin.password',
                        ],
                    ],
                ],
            ],
        ]);
        $this->assertEquals(trans('validation.confirmed', ['attribute' => 'admin.password']), $response->json('errors.0.extensions.validation')['admin.password'][0]);
    }

    #[Test]
    public function errorCreateAdminWithInactiveCompany(): void
    {
        tenant()->updateOrFail(['status' => CompanyStatusEnum::INACTIVE]);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(['admin' => $this->_input])
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals(__('api.inactive_company'), $response->json('errors.0.message'));
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $query['query'] = '
            mutation CreateAdmin($admin: AdminInputGraphQLType!) {
                createAdmin(admin: $admin) {
                    id
                    username
                    first_name
                    last_name
                    phone
                    status
                    language
                    role_id
                    created_at
                    updated_at
                    deleted_at
                    avatar {
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
                    template_theme {
                        layout_name
                    }
                    role {
                        id
                        name
                        created_at
                        updated_at
                        deleted_at
                    }
                }
            }
        ';

        return $query;
    }

    private function assertsSuccessResponse(TestResponse $response): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'createAdmin' => [
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
                    'avatar',
                    'template_theme',
                    'role' => [
                        'id',
                        'name',
                        'created_at',
                        'updated_at',
                        'deleted_at',
                    ],
                ],
            ],
        ]);

        $admin = CompanyAdmin::query()->find($response->json('data.createAdmin.id'));
        $this->assertNotNull($admin);

        $this->assertEquals($admin->username, $this->_input['username']);
        $this->assertEquals($admin->first_name, $this->_input['first_name']);
        $this->assertEquals($admin->last_name, $this->_input['last_name']);
        $this->assertEquals($admin->status->value, $this->_input['status']);
        $this->assertEquals($admin->role_id, $this->_input['role_id']);
        $this->assertTrue(Hash::check($this->_input['password'], $admin->password));

        $this->assertEquals($admin->username, $response->json('data.createAdmin.username'));
        $this->assertEquals($admin->first_name, $response->json('data.createAdmin.first_name'));
        $this->assertEquals($admin->last_name, $response->json('data.createAdmin.last_name'));
        $this->assertEquals($admin->phone, $response->json('data.createAdmin.phone'));
        $this->assertEquals($admin->status->value, $response->json('data.createAdmin.status'));
        $this->assertEquals($admin->language, $response->json('data.createAdmin.language'));
        $this->assertEquals($admin->role_id, $response->json('data.createAdmin.role_id'));
        $this->assertEquals($admin->created_at->timestamp, $response->json('data.createAdmin.created_at'));
        $this->assertEquals($admin->updated_at->timestamp, $response->json('data.createAdmin.updated_at'));
        $this->assertNull($admin->avatar);
        $this->assertNull($admin->template_theme?->layout_name);
        $this->assertNull($response->json('data.createAdmin.avatar'));
        $this->assertNull($response->json('data.createAdmin.template_theme'));
    }
}
