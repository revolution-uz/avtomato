<?php

namespace Tests\GraphQL\Management\Mutations;

use App\Models\Company\BalanceFund;
use App\Models\Company\CardConfirmationSms;
use Illuminate\Support\Facades\Http;
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
        'expiry' => '2505',
        'phone' => null,
    ];

    protected function setUp(): void
    {
        parent::setUp();

        AtmosFaker::getToken();
        AtmosFaker::createPayment();
        AtmosFaker::preConfirmPayment();
        AtmosFaker::confirmPayment();
    }

    #[Test]
    public function successBalanceFillConfirmWithoutPhone()
    {
        AtmosFaker::confirmCard();

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.management'), $this->buildGraphQLQuery($this->_input));

        $this->assertBalanceFillConfirmResponse($response);
        $this->assertNull($response->json('data.balanceFillConfirm.phone_number'));
    }

    #[Test]
    public function successBalanceFillConfirmWithPhone()
    {
        AtmosFaker::confirmCard();

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(
                route('graphql.management'),
                $this->buildGraphQLQuery(array_merge($this->_input, ['phone' => '+998900222222'])),
            );

        $this->assertBalanceFillConfirmResponse($response);
        $this->assertEquals('+998900222222', $response->json('data.balanceFillConfirm.phone_number'));
    }

    #[Test]
    public function successBalanceFillConfirmWithAttachedCard()
    {
        AtmosFaker::attachCard(true);

        /** @var CardConfirmationSms $confirmationSms */
        $confirmationSms = CardConfirmationSms::factory()->create();

        $this->_input = array_merge($this->_input, ['otp' => $confirmationSms->code, 'phone' => $confirmationSms->phone]);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.management'), $this->buildGraphQLQuery($this->_input));

        $this->assertBalanceFillConfirmResponse($response);
        $this->assertEquals($confirmationSms->phone, $response->json('data.balanceFillConfirm.phone_number'));
    }

    #[Test]
    public function failBalanceFillConfirmWithAttachedCard()
    {
        Http::fake([
            'https://partner.atmos.uz/partner/bind-card/confirm' => Http::response([
                'result' => [
                    'code' => 'STPIMS-ERR-080',
                    'description' => 'Карта заблокирована по причине ввода неправильного PIN-кода',
                ],
                'data' => null,
            ]),
        ]);

        /** @var CardConfirmationSms $confirmationSms */
        $confirmationSms = CardConfirmationSms::factory()->create();

        $this->_input = array_merge($this->_input, ['otp' => '999999', 'phone' => $confirmationSms->phone]);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.management'), $this->buildGraphQLQuery($this->_input));

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals('Карта заблокирована по причине ввода неправильного PIN-кода', $response->json('errors.0.extensions.validation.otp.0'));
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        $query = [];
        $query['variables'] = $variables;
        $query['query'] = '
            mutation BalanceFillConfirm($transaction_id: BigInteger!, $otp: String!, $amount: Int!, $number: BigInteger!, $expiry: String!, $phone: BigInteger) {
                balanceFillConfirm(transaction_id: $transaction_id, otp: $otp, amount: $amount, number: $number, expiry: $expiry, phone: $phone) {
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
