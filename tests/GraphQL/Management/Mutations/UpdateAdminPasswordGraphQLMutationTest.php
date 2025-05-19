<?php

namespace Tests\GraphQL\Management\Mutations;

use App\Enums\Company\CompanyStatusEnum;
use App\Models\Company\CompanyAdmin;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class UpdateAdminPasswordGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private CompanyAdmin $_adminInput;

    private string $_password = 'password';

    protected function setUp(): void
    {
        parent::setUp();

        $this->_adminInput = CompanyAdmin::factory()->create();
    }

    #[Test]
    public function successUpdateAdminPassword()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery([
                    'id' => $this->_adminInput->id,
                    'password' => $this->_password,
                    'password_confirmation' => $this->_password,
                ])
            );

        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'updateAdminPassword',
            ],
        ]);
        $this->_adminInput->refresh();
        $this->assertTrue($response->json('data.updateAdminPassword'));
        $this->assertTrue(Hash::check($this->_password, $this->_adminInput->password));
    }

    #[Test]
    public function errorWithWrongPasswordConfirmation()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery([
                    'id' => $this->_adminInput->id,
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
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery([
                    'id' => null,
                    'password' => null,
                    'password_confirmation' => null,
                ])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$id" of non-null type "Int!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$password" of non-null type "String!" must not be null.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$password_confirmation" of non-null type "String!" must not be null.'));

        $password = Str::random(7);
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery([
                    'id' => $this->_adminInput->id,
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

    #[Test]
    public function errorUpdateAdminPasswordWithInactiveCompany(): void
    {
        tenant()->updateOrFail(['status' => CompanyStatusEnum::INACTIVE]);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery([
                    'id' => $this->_adminInput->id,
                    'password' => $this->_password,
                    'password_confirmation' => $this->_password,
                ])
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
            mutation UpdateAdminPassword($id: Int!, $password: String!, $password_confirmation: String!) {
                updateAdminPassword(id: $id, password: $password, password_confirmation: $password_confirmation)
            }
        ';

        return $query;
    }
}
