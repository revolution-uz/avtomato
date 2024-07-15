<?php

namespace Management\Mutations;

use PHPUnit\Framework\Attributes\Test;
use Tests\Fakers\AtmosFaker;
use Tests\GraphQL\GraphQLTestCase;

class BalanceFillConfirmGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    #[Test]
    public function successBalanceFillConfirm()
    {
        $admin = $this->createAdminUser();
        AtmosFaker::getToken();
        AtmosFaker::confirmCard();
        AtmosFaker::createPayment();
        AtmosFaker::preConfirmPayment();
        AtmosFaker::confirmPayment();

        $response = $this
            ->actingAs($admin)
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery([
                    'transaction_id' => 1,
                    'amount' => 1000000,
                    'otp' => '123456',
                    'number' => '9860082443467252',
                    'phone' => '+998900222222',
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
            mutation BalanceFillConfirm($transaction_id: BigInteger!, $otp: String!, $amount: Int!, $number: BigInteger!, $phone: BigInteger!) {
                balanceFillConfirm(transaction_id: $transaction_id, otp: $otp, amount: $amount, number: $number, phone: $phone) {
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
