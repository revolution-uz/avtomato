<?php

namespace Tests\GraphQL\Management\Mutations;

use App\Models\Company\CompanyAdmin;
use App\Models\Company\Rbac\Role;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class ChangeProfilePasswordGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private CompanyAdmin $_admin;

    private string $_oldPassword = 'old-password';
    private string $_password = 'password';

    protected function setUp(): void
    {
        parent::setUp();

        $this->_admin = CompanyAdmin::factory()->create([
            'role_id' => Role::getAdmin()->id,
            'password' => Hash::make($this->_oldPassword),
        ]);
    }

    #[Test]
    public function successChangeProfilePassword()
    {
        $response = $this
            ->actingAs($this->_admin)
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery([
                    'old_password' => $this->_oldPassword,
                    'password' => $this->_password,
                    'password_confirmation' => $this->_password,
                ])
            );

        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'changeProfilePassword',
            ],
        ]);
        $this->assertTrue($response->json('data.changeProfilePassword'));
        $this->assertTrue(Hash::check($this->_password, $this->_admin->password));
    }

    #[Test]
    public function errorWithWrongOldPassword()
    {
        $response = $this
            ->actingAs($this->_admin)
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery([
                    'old_password' => Str::random(8),
                    'password' => $this->_password,
                    'password_confirmation' => $this->_password,
                ])
            );
        $response->assertOk();
        $response->assertJsonStructure([
            'errors' => [
                [
                    'message',
                    'extensions' => [
                        'validation' => [
                            'old_password',
                        ],
                    ],
                ],
            ],
        ]);
        $this->assertEquals('The old password is incorrect.', $response->json('errors.0.extensions.validation.old_password.0'));
    }

    #[Test]
    public function errorWithWrongPasswordConfirmation()
    {
        $response = $this
            ->actingAs($this->_admin)
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery([
                    'old_password' => $this->_oldPassword,
                    'password' => $this->_password,
                    'password_confirmation' => Str::random(8),
                ])
            );
        $response->assertOk();
        $response->assertJsonStructure([
            'errors' => [
                [
                    'message',
                    'extensions' => [
                        'validation' => [
                            'password',
                        ],
                    ],
                ],
            ],
        ]);
        $this->assertEquals(trans('validation.confirmed', ['attribute' => 'Пароль']), $response->json('errors.0.extensions.validation.password.0'));
    }

    #[Test]
    public function errorArgumentsValidation()
    {
        $response = $this
            ->actingAs($this->_admin)
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery([
                    'old_password' => null,
                    'password' => null,
                    'password_confirmation' => null,
                ])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$old_password" of non-null type "String!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$password" of non-null type "String!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$password_confirmation" of non-null type "String!" must not be null.'));

        $password = Str::random(7);
        $response = $this
            ->actingAs($this->_admin)
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery([
                    'old_password' => $this->_oldPassword,
                    'password' => $password,
                    'password_confirmation' => $password,
                ])
            );
        $response->assertOk();
        $response->assertJsonStructure([
            'errors' => [
                [
                    'message',
                    'extensions' => [
                        'validation' => [
                            'password',
                        ],
                    ],
                ],
            ],
        ]);
        $this->assertEquals(trans('validation.min.string', ['attribute' => 'Пароль', 'min' => 8]), $response->json('errors.0.extensions.validation.password.0'));
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $query['query'] = '
            mutation ChangeProfilePassword ($old_password: String!, $password: String!, $password_confirmation: String!) {
                changeProfilePassword(old_password: $old_password, password: $password, password_confirmation: $password_confirmation)
            }
        ';

        return $query;
    }
}
