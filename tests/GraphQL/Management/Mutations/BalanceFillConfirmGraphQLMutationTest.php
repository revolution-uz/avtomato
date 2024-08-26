<?php

namespace Tests\GraphQL\Management\Mutations;

use App\Models\Company\BalanceFund;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\Fakers\AtmosFaker;
use Tests\GraphQL\GraphQLTestCase;

class BalanceFillConfirmGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private array $_input = [
        'transaction_id' => 1,
        'amount' => 1000000,
        'otp' => '123456',
        'number' => '9860082443467252',
        'phone' => null,
    ];

    protected function setUp(): void
    {
        parent::setUp();

        AtmosFaker::getToken();
        AtmosFaker::confirmCard();
        AtmosFaker::createPayment();
        AtmosFaker::preConfirmPayment();
        AtmosFaker::confirmPayment();
    }

    #[Test]
    public function successBalanceFillConfirmWithoutPhone()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery($this->_input),
            );

        $this->assertBalanceFillConfirmResponse($response);
        $this->assertNull($response->json('data.balanceFillConfirm.phone_number'));
    }

    #[Test]
    public function successBalanceFillConfirmWithPhone()
    {
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(array_merge($this->_input, ['phone' => '+998900222222'])),
            );

        $this->assertBalanceFillConfirmResponse($response);
        $this->assertEquals('+998900222222', $response->json('data.balanceFillConfirm.phone_number'));
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $query['query'] = '
            mutation BalanceFillConfirm($transaction_id: BigInteger!, $otp: String!, $amount: Int!, $number: BigInteger!, $phone: BigInteger) {
                balanceFillConfirm(transaction_id: $transaction_id, otp: $otp, amount: $amount, number: $number, phone: $phone) {
                    id
                    admin_id
                    amount
                    phone_number
                    external_transaction_id
                    card_number
                    created_at
                }
            }
        ';

        return $query;
    }

    private function assertBalanceFillConfirmResponse(TestResponse $response)
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure([
            'data' => [
                'balanceFillConfirm' => [
                    'id',
                    'admin_id',
                    'amount',
                    'phone_number',
                    'external_transaction_id',
                    'card_number',
                    'created_at',
                ],
            ],
        ]);

        $balanceFund = BalanceFund::query()->find($response->json('data.balanceFillConfirm.id'));
        $this->assertEquals($balanceFund->id, $response->json('data.balanceFillConfirm.id'));
        $this->assertEquals($balanceFund->admin_id, $response->json('data.balanceFillConfirm.admin_id'));
        $this->assertEquals($balanceFund->amount, $response->json('data.balanceFillConfirm.amount'));
        $this->assertEquals($balanceFund->phone_number, $response->json('data.balanceFillConfirm.phone_number'));
        $this->assertEquals($balanceFund->external_transaction_id, $response->json('data.balanceFillConfirm.external_transaction_id'));
        $this->assertEquals($balanceFund->card_number, $response->json('data.balanceFillConfirm.card_number'));
        $this->assertEquals($balanceFund->created_at->timestamp, $response->json('data.balanceFillConfirm.created_at'));
    }
}
