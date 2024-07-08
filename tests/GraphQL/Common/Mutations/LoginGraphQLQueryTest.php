<?php

namespace Common\Mutations;

use App\Models\Company\User;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class LoginGraphQLQueryTest extends GraphQLTestCase
{
    private User $_user;

    private array $_headers = [
        'Device-Name' => 'test',
        'Device-OS' => 'android',
        'Device-Type' => 'mobile',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        User::query()->forceDelete();
        $this->_user = User::factory()->create([
            'username' => 'test',
            'password' => bcrypt('password'),
        ]);
    }

    #[Test]
    public function successLogin()
    {
        $response = $this
            ->postJson(
                route('graphql.common'),
                $this->buildGraphQLQuery($this->_user->username, 'password'),
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
                $this->buildGraphQLQuery($this->_user->username, 'not-password'),
                $this->_headers
            );

        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'errors' => [
                '*' => [
                    'message',
                ],
            ],
        ]);
        $this->assertEquals($response->json('errors.0.message'), 'Login or password is wrong');
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
        $this->assertEquals($this->_user->id, $responseCurrentProfile->json('data.currentProfile.id'));
        $this->assertEquals($this->_user->username, $responseCurrentProfile->json('data.currentProfile.username'));
    }
}
