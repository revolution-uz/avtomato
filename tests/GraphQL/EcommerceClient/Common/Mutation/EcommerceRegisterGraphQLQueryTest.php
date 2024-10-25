<?php

namespace Tests\GraphQL\EcommerceClient\Common\Mutation;

use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class EcommerceRegisterGraphQLQueryTest extends GraphQLTestCase
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
                route('graphql.ecommerceCommon'),
                $this->buildGraphQLQuery('998904333699'),
                $this->_headers
            );

        $this->assertSuccessRegisterResponse($response);
    }

    #[Test]
    public function successRegister()
    {
        $response = $this
            ->postJson(
                route('graphql.ecommerceCommon'),
                $this->buildGraphQLQuery('998904333698'),
                $this->_headers
            );
        $this->assertSuccessRegisterResponse($response);
    }

    private function buildGraphQLQuery(string $phone): array
    {
        $query = [];
        $query['variables'] = ['phone' => $phone];
        $query['query'] = '
            mutation EcommerceRegister($phone: BigInteger!) {
                ecommerceRegister(phone: $phone)
            }
        ';

        return $query;
    }

    private function assertSuccessRegisterResponse(TestResponse $response): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'ecommerceRegister',
            ],
        ]);
        $this->assertTrue($response->json('data.ecommerceRegister'));
    }
}
