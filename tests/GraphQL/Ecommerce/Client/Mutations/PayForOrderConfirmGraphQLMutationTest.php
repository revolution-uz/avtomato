<?php

namespace Tests\GraphQL\Ecommerce\Client\Mutations;

use App\Enums\Company\PaymentStatusEnum;
use App\Enums\FeatureEnum;
use App\Models\Company\Ecommerce\Payment;
use App\Models\Company\Ecommerce\User;
use App\Models\Company\Feature;
use Illuminate\Support\Arr;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\Fakers\AtmosFaker;
use Tests\GraphQL\GraphQLTestCase;

class PayForOrderConfirmGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private array $_input = ['payment_id' => null, 'otp' => '111111'];

    private User $_user;

    private Payment $_payment;

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
        $this->_payment = Payment::factory()->notPaid()->create(['user_id' => $this->_user->id]);
        $this->_input['payment_id'] = $this->_payment->id;

        AtmosFaker::getToken();
    }

    #[Test]
    public function successPayForOrderConfirm()
    {
        AtmosFaker::confirmPayment();

        $response = $this
            ->actingAs($this->_user, 'ecommerce-client')
            ->postJson(route('graphql.ecommerce-client'), $this->buildGraphQLQuery($this->_input));

        $this->assertSuccessResponse($response);
    }

    #[Test]
    public function errorPayForOrderConfirmWithWrongOtp()
    {
        AtmosFaker::confirmPayment(true);

        $response = $this
            ->actingAs($this->_user, 'ecommerce-client')
            ->postJson(route('graphql.ecommerce-client'), $this->buildGraphQLQuery($this->_input));

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals('OTP код указан неверно', $response->json('errors.0.extensions.validation.otp.0'));
    }

    #[Test]
    public function errorArgumentsValidation()
    {
        $response = $this
            ->actingAs($this->_user, 'ecommerce-client')
            ->postJson(route('graphql.ecommerce-client'), $this->buildGraphQLQuery());
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$payment_id" of required type "Int!" was not provided.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$otp" of required type "String!" was not provided.'));

        $response = $this
            ->actingAs($this->_user, 'ecommerce-client')
            ->postJson(
                route('graphql.ecommerce-client'),
                $this->buildGraphQLQuery(array_merge($this->_input, ['payment_id' => 999999999, 'otp' => '111111']))
            );
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'payment id']), $response->json('errors.0.extensions.validation.payment_id.0'));
    }

    protected function buildGraphQLQuery(array $variables = []): array
    {
        return [
            'query' => <<<GRAPHQL
                mutation PayForOrderConfirm(\$payment_id: Int!, \$otp: String!) {
                    payForOrderConfirm(payment_id: \$payment_id, otp: \$otp) {
                        {$this->convertIntoQuery($this->_structure)}
                    }
                }
            GRAPHQL,
            'variables' => $variables,
        ];
    }

    private function assertSuccessResponse(TestResponse $response)
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure(['data' => ['payForOrderConfirm' => $this->_structure]]);

        $this->_payment->refresh();

        $this->assertEquals(PaymentStatusEnum::SUCCESS, $this->_payment->status);
        $this->assertNotNull($this->_payment->external_id);
        $this->assertNotNull($this->_payment->paid_at);

        $this->assertEquals($this->_payment->id, $response->json('data.payForOrderConfirm.id'));
        $this->assertEquals($this->_payment->amount, $response->json('data.payForOrderConfirm.amount'));
        $this->assertEquals($this->_payment->card_pan, $response->json('data.payForOrderConfirm.card_pan'));
        $this->assertEquals($this->_payment->status->value, $response->json('data.payForOrderConfirm.status'));
        $this->assertEquals($this->_payment->paid_at?->timestamp, $response->json('data.payForOrderConfirm.paid_at'));
        $this->assertEquals($this->_payment->external_id, $response->json('data.payForOrderConfirm.external_id'));
        $this->assertEquals($this->_payment->order_id, $response->json('data.payForOrderConfirm.order_id'));
        $this->assertEquals($this->_payment->transaction_type_id, $response->json('data.payForOrderConfirm.transaction_type_id'));
        $this->assertEquals($this->_payment->user_id, $response->json('data.payForOrderConfirm.user_id'));
        $this->assertEquals($this->_payment->admin_id, $response->json('data.payForOrderConfirm.admin_id'));
        $this->assertEquals($this->_payment->created_at->timestamp, $response->json('data.payForOrderConfirm.created_at'));
        $this->assertEquals($this->_payment->updated_at->timestamp, $response->json('data.payForOrderConfirm.updated_at'));
    }
}
