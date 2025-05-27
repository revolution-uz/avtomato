<?php

namespace Tests\GraphQL\Ecommerce\Client\Mutations;

use App\Enums\Company\PaymentStatusEnum;
use App\Enums\FeatureEnum;
use App\Models\Company\Ecommerce\Order;
use App\Models\Company\Ecommerce\Payment;
use App\Models\Company\Ecommerce\User;
use App\Models\Company\Feature;
use App\Models\Company\TransactionType;
use Illuminate\Support\Arr;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\Fakers\AtmosFaker;
use Tests\GraphQL\GraphQLTestCase;

class PayForOrderGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private array $_input = ['number' => 8600332914249390, 'expiry' => '2509', 'order_id' => null];

    private User $_user;

    private array $_structure = [
        'id',
        'amount',
        'card_pan',
        'status',
        'paid_at',
        'external_id',
        'order_id',
        'transaction_type_id',
        'user_id',
        'admin_id',
        'created_at',
        'updated_at',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $feature = Feature::findByName(FeatureEnum::ATMOS_CARD_PAY);
        $feature->data = [
            'username' => 'username',
            'password' => 'password',
            'store_id' => '1',
            'expires_in' => 3600,
            'access_token' => 'access_token',
            'token_type' => 'token_type',
        ];
        $feature->saveOrFail();

        Payment::query()->forceDelete();
        $this->_user = $this->createEcommerceClientUser();
        $this->_input['order_id'] = Order::factory()->create(['user_id' => $this->_user->id])->id;

        AtmosFaker::getToken();
    }

    #[Test]
    public function successPayForOrder()
    {
        AtmosFaker::createPayment();
        AtmosFaker::preConfirmPayment();

        $response = $this
            ->actingAs($this->_user, 'ecommerce-client')
            ->postJson(route('graphql.ecommerce-client'), $this->buildGraphQLQuery($this->_input));

        $this->assertSuccessPayForOrderResponse($response);
    }

    #[Test]
    public function errorPayForOrder()
    {
        AtmosFaker::createPayment(true);

        $response = $this
            ->actingAs($this->_user, 'ecommerce-client')
            ->postJson(route('graphql.ecommerce-client'), $this->buildGraphQLQuery($this->_input));

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull('Ошибка', $response->json('errors.0.message'));
    }

    #[Test]
    public function errorValidation(): void
    {
        // GraphQL type errors
        $response = $this
            ->actingAs($this->_user, 'ecommerce-client')
            ->postJson(route('graphql.ecommerce-client'), $this->buildGraphQLQuery());
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$number" of required type "BigInteger!" was not provided.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$expiry" of required type "String!" was not provided.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$order_id" of required type "Int!" was not provided.'));

        // Rule errors
        $response = $this
            ->actingAs($this->_user, 'ecommerce-client')
            ->postJson(
                route('graphql.ecommerce-client'),
                $this->buildGraphQLQuery(['number' => '123456789012345', 'expiry' => '0123', 'order_id' => 999999999])
            );
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(__('validation.digits', ['attribute' => 'number', 'digits' => 16]), $response->json('errors.0.extensions.validation.number.0'));
        $this->assertEquals(__('validation.date_format', ['attribute' => 'expiry', 'format' => 'ym']), $response->json('errors.0.extensions.validation.expiry.0'));
        $this->assertEquals(__('validation.exists', ['attribute' => 'order id']), $response->json('errors.0.extensions.validation.order_id.0'));
    }

    protected function buildGraphQLQuery(array $variables = []): array
    {
        return [
            'query' => <<<GRAPHQL
                mutation PayForOrder(\$number: BigInteger!, \$expiry: String!, \$order_id: Int!) {
                    payForOrder(number: \$number, expiry: \$expiry, order_id: \$order_id) {
                        {$this->convertIntoQuery($this->_structure)}
                    }
                }
            GRAPHQL,
            'variables' => $variables,
        ];
    }

    private function assertSuccessPayForOrderResponse(TestResponse $response): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure(['data' => ['payForOrder' => $this->_structure]]);

        /** @var Payment $payment */
        $payment = $this->_user->payments()->first();

        $this->assertNotNull($payment);
        $this->assertNotNull($payment->amount);
        $this->assertEquals('860033******9390', $payment->card_pan);
        $this->assertEquals(PaymentStatusEnum::WAITING_FOR_CONFIRMATION, $payment->status);
        $this->assertNull($payment->paid_at);
        $this->assertNotNull($payment->external_id);
        $this->assertEquals($this->_input['order_id'], $payment->order_id);
        $this->assertEquals(TransactionType::ATMOS_WITH_CARD, $payment->transaction_type_id);
        $this->assertNull($payment->admin_id);

        $this->assertEquals($payment->id, $response->json('data.payForOrder.id'));
        $this->assertEquals($payment->amount, $response->json('data.payForOrder.amount'));
        $this->assertEquals($payment->card_pan, $response->json('data.payForOrder.card_pan'));
        $this->assertEquals($payment->status->value, $response->json('data.payForOrder.status'));
        $this->assertEquals($payment->paid_at?->timestamp, $response->json('data.payForOrder.paid_at'));
        $this->assertEquals($payment->external_id, $response->json('data.payForOrder.external_id'));
        $this->assertEquals($payment->order_id, $response->json('data.payForOrder.order_id'));
        $this->assertEquals($payment->transaction_type_id, $response->json('data.payForOrder.transaction_type_id'));
        $this->assertEquals($payment->user_id, $response->json('data.payForOrder.user_id'));
        $this->assertEquals($payment->admin_id, $response->json('data.payForOrder.admin_id'));
        $this->assertEquals($payment->created_at?->timestamp, $response->json('data.payForOrder.created_at'));
        $this->assertEquals($payment->updated_at?->timestamp, $response->json('data.payForOrder.updated_at'));
    }
}
