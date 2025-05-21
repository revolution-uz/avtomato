<?php

namespace Tests\GraphQL\Common\Mutations;

use App\Models\Company\CompanyAdmin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class LoginGraphQLMutationTest extends GraphQLTestCase
{
    private CompanyAdmin $_admin;

    private array $_headers = [
        'Device-Name' => 'test',
        'Device-OS' => 'android',
        'Device-Type' => 'mobile',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        CompanyAdmin::query()->forceDelete();
        $this->_admin = CompanyAdmin::factory()->create([
            'username' => 'test',
            'password' => Hash::make('password'),
        ]);
    }

    #[Test]
    public function successLogin()
    {
        $response = $this
            ->postJson(
                route('graphql.common'),
                $this->buildGraphQLQuery($this->_admin->username, 'password'),
                $this->_headers
            );

        $this->assertSuccessLoginResponse($response);
    }

    #[Test]
    public function errorLogin()
    {
        $response = $this
            ->postJson(
                route('graphql.common'),
                $this->buildGraphQLQuery($this->_admin->username, 'not-password'),
                $this->_headers
            );

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals('Логин или пароль неверны', $response->json('errors.0.extensions.validation.username.0'));
    }

    private function buildGraphQLQuery(string $username, string $password): array
    {
        $query = [];
        $query['variables'] = ['username' => $username, 'password' => $password];
        $query['query'] = '
            mutation Login($username: String!, $password: String!) {
                login(username: $username, password: $password)
            }
        ';

        return $query;
    }

    private function assertSuccessLoginResponse(TestResponse $response)
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'login',
            ],
        ]);
        $this->assertIsString($response->json('data.login'));

        $query['query'] = '
            query CurrentProfile {
                currentProfile {
                    id
                    username
                }
            }
        ';
        $responseCurrentProfile = $this
            ->postJson(
                route('graphql.management'),
                $query,
                ['Authorization' => 'Bearer ' . $response->json('data.login')]
            );
        $responseCurrentProfile->assertOk();
        $responseCurrentProfile->assertJsonIsObject();
        $responseCurrentProfile->assertJsonStructure([
            'data' => [
                'currentProfile' => [
                    'id',
                    'username',
                ],
            ],
        ]);
        $this->assertEquals($this->_admin->id, $responseCurrentProfile->json('data.currentProfile.id'));
        $this->assertEquals($this->_admin->username, $responseCurrentProfile->json('data.currentProfile.username'));
    }
}
