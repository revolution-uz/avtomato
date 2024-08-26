<?php

namespace Tests\GraphQL\Management\Mutations;


use PHPUnit\Framework\Attributes\Test;
use Tests\Fakers\AtmosFaker;
use Tests\GraphQL\GraphQLTestCase;

class BalanceFillGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    #[Test]
    public function successBalanceFill()
    {
        $admin = $this->createAdminUser();
        AtmosFaker::getToken();
        AtmosFaker::attachCard();

        $response = $this
            ->actingAs($admin)
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery([
                    'number' => 9860082443467252,
                    'expiry' => '2808',
                ]),
            );

        $response->assertSuccessful();

        $this->assertIsArray($response->json());
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $query['query'] = '
            mutation BalanceFill($number: BigInteger!, $expiry: String!) {
                balanceFill(number: $number, expiry: $expiry) {
                    transaction_id
                    phone
                    result_code
                    result_description
                }
            }
        ';

        return $query;
    }
}
