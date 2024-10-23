<?php

namespace Tests\GraphQL\Common\Mutations;

use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class MarketRegisterGraphQLQueryTest extends GraphQLTestCase
{
    private array $_headers = [
        'Device-Name' => 'test',
        'Device-OS' => 'android',
        'Device-Type' => 'mobile',
    ];

    #[Test]
    public function successRegisterByAllowedPhone()
    {
        $response = $this
            ->postJson(
                route('graphql.common'),
                $this->buildGraphQLQuery('998904333699'),
                $this->_headers
            );

        $this->assertSuccessLoginResponse($response);
    }

    #[Test]
    public function successRegister()
    {
        $response = $this
            ->postJson(
                route('graphql.common'),
                $this->buildGraphQLQuery('998904333698'),
                $this->_headers
            );
        $this->assertSuccessLoginResponse($response);
    }

    //    #[Test]
    //    public function errorLogin()
    //    {
    //        $response = $this
    //            ->postJson(
    //                route('graphql.common'),
    //                $this->buildGraphQLQuery($this->_admin->username, 'not-password'),
    //                $this->_headers
    //            );
    //
    //        $response->assertOk();
    //        $response->assertJsonIsObject();
    //        $response->assertJsonStructure([
    //            'errors' => [
    //                '*' => [
    //                    'message',
    //                ],
    //            ],
    //        ]);
    //        $this->assertEquals($response->json('errors.0.message'), 'Login or password is wrong');
    //    }

    private function buildGraphQLQuery(string $phone): array
    {
        $query = [];
        $query['variables'] = ['phone' => $phone];
        $query['query'] = '
            mutation MarketRegister($phone: BigInteger!) {
                marketRegister(phone: $phone)
            }
        ';

        return $query;
    }

    private function assertSuccessLoginResponse(TestResponse $response): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'marketRegister',
            ],
        ]);
        $this->assertTrue($response->json('data.marketRegister'));
    }
}
