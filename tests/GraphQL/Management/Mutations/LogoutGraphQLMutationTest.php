<?php

namespace Management\Mutations;

use App\Models\Company\CompanyAdmin;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class LogoutGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    #[Test]
    public function successLogout()
    {
        $admin = CompanyAdmin::factory()->create();
        $token = $admin->createTokenWithData(
            'test',
            [
                'device_type' => 'mobile',
                'device_os' => 'Android',
                'device_ip' => '192.168.1.1',
            ]
        );

        $response = $this
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(),
                ['Authorization' => 'Bearer ' . $token]
            );

        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'logout',
            ],
        ]);
        $this->assertTrue($response->json('data.logout'));

        // TODO: Uncomment after Logout is fixed
        // $response = $this
        //     ->postJson(
        //         route('graphql.management'),
        //         $this->buildGraphQLQuery(),
        //         ['Authorization' => 'Bearer ' . $token]
        //     );
        // $response->assertStatus(401);
        // $this->assertArrayHasKey('message', $response->json());
        // $this->assertEquals('Unauthenticated.', $response->json('message'));
    }

    #[Test]
    public function errorLogout()
    {
        $response = $this
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(),
                ['Authorization' => 'Bearer ' . Str::random(50)]
            );

        $response->assertStatus(401);
        $this->assertArrayHasKey('message', $response->json());
        $this->assertEquals('Unauthenticated.', $response->json('message'));
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $query['query'] = '
            mutation Logout {
                logout
            }
        ';

        return $query;
    }
}
