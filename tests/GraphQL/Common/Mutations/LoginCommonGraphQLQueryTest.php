<?php

namespace Common\Mutations;

use App\Models\Company\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class LoginCommonGraphQLQueryTest extends GraphQLTestCase
{
    #[Test]
    public function successLogin()
    {
        $user = User::factory()->create([
            'username' => 'test',
            'password' => bcrypt('password'),
        ]);

        $response = $this
            ->postJson(
                route('graphql.common'),
                $this->buildGraphQLQuery($user->username, 'password'),
                ['Device-Name' => 'test', 'Device-OS' => 'android', 'Device-Type' => 'mobile']
            );

        $response->assertOk();
        $this->assertIsString($response->json('data.login'));
    }

    private function buildGraphQLQuery(string $username, string $password): array
    {
        $query = [];
        $query['variables'] = ['username' => $username, 'password' => $password];
        $query['query'] = '
            mutation ($username: String!, $password: String!) {
                login(username: $username, password: $password)
            }
        ';

        return $query;
    }
}
