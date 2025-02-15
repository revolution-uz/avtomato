<?php

namespace Tests\GraphQL\Ecommerce\Client\Mutations;

use App\Enums\Company\AddressTypeEnum;
use App\Enums\Company\OrderDeliveryTypeEnum;
use App\Enums\Company\OrderPaymentMethodEnum;
use App\Models\Company\Address;
use App\Models\Company\Ecommerce\DeliveryPoint;
use App\Models\Company\Ecommerce\Order;
use App\Models\Company\Ecommerce\ProductVariation;
use App\Models\Company\Ecommerce\ShippingPrice;
use App\Models\Company\Ecommerce\User;
use App\Models\Company\Installment;
use Illuminate\Support\Arr;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class CreateOrderGraphQLMutationTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private User $_user;

    private array $_structure = [
        'id',
        'delivery_type',
        'payment_method',
        'shipping_price',
        'user_id',
        'address_id',
        'delivery_point_id',
        'status_id',
        'installment_id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->_user = $this->createEcommerceClientUser(true);
        Order::query()->forceDelete();
    }

    #[Test]
    public function successCreateOrderWithSelfPickupType(): void
    {
        $variables = $this->getInputVariables(variables: [
            'payment_method' => OrderPaymentMethodEnum::CARD,
            'delivery_type' => OrderDeliveryTypeEnum::SELF_PICKUP,
            'delivery_point_id' => DeliveryPoint::factory()->create()->id,
            'address_id' => null,
        ]);

        $response = $this
            ->actingAs($this->_user, 'ecommerce-client')
            ->postJson(route('graphql.ecommerce-client'), $this->buildGraphQLQuery($variables));

        $this->assertSuccessCreateOrderResponse($response, $variables);

        $order = Order::query()->first();
        $this->assertNotNull($order);

        $this->assertEquals($variables['delivery_type'], $order->delivery_type);
        $this->assertEquals($variables['payment_method'], $order->payment_method);
        $this->assertNull($order->address_id);
        $this->assertEquals($variables['delivery_point_id'], $order->delivery_point_id);
        $this->assertNull($order->installment_id);

        $this->assertEquals(count($variables['cart']), $order->orderItems->count());
        $orderItem = $order->orderItems()->first();
        $this->assertEquals($variables['cart'][0]['variation_id'], $orderItem->variation_id);
        $this->assertEquals($variables['cart'][0]['quantity'], $orderItem->quantity);
    }

    #[Test]
    public function successCreateOrderWithDeliveryPlaceType(): void
    {
        $address = Address::factory()->create(['type' => AddressTypeEnum::DELIVERY_PLACE]);
        /** @var ShippingPrice $shippingPrice */
        $shippingPrice = ShippingPrice::factory()->create([
            'region_id' => $address->region_id,
            'district_id' => $address->district_id,
            'mahalla_id' => $address->mahalla_id,
        ]);

        $variables = $this->getInputVariables(variables: [
            'payment_method' => OrderPaymentMethodEnum::CARD,
            'delivery_type' => OrderDeliveryTypeEnum::DELIVERY_TO_ADDRESS,
            'address_id' => $address->id,
            'delivery_point_id' => null,
        ]);

        $response = $this
            ->actingAs($this->_user, 'ecommerce-client')
            ->postJson(route('graphql.ecommerce-client'), $this->buildGraphQLQuery($variables));

        $this->assertSuccessCreateOrderResponse($response, $variables);

        $order = Order::query()->first();
        $this->assertNotNull($order);

        $this->assertEquals($variables['payment_method'], $order->payment_method);
        $this->assertEquals($variables['delivery_type'], $order->delivery_type);
        $this->assertEquals($variables['address_id'], $order->address_id);
        $this->assertEquals($shippingPrice->price, $order->shipping_price);
        $this->assertNull($order->delivery_point_id);
        $this->assertNull($order->installment_id);

        $this->assertEquals(count($variables['cart']), $order->orderItems->count());
        $orderItem = $order->orderItems()->first();
        $this->assertEquals($variables['cart'][0]['variation_id'], $orderItem->variation_id);
        $this->assertEquals($variables['cart'][0]['quantity'], $orderItem->quantity);
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
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$delivery_type" of required type "Int!" was not provided.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$payment_method" of required type "Int!" was not provided.'));
        $this->assertNotNull(Arr::first($response->json('errors'), fn ($item) => $item['message'] === 'Variable "$cart" of required type "[UserCartInputGraphQLType!]!" was not provided.'));

        // Rule errors
        $variables = $this->getInputVariables(variables: [
            'delivery_type' => 9,
            'payment_method' => 9,
            'address_id' => 999999999,
            'delivery_point_id' => 999999999,
            'installment_id' => 999999999,
            'cart' => [
                [
                    'variation_id' => 999999999,
                    'quantity' => 0,
                ],
            ],
        ]);
        $response = $this
            ->actingAs($this->_user, 'ecommerce-client')
            ->postJson(route('graphql.ecommerce-client'), $this->buildGraphQLQuery($variables));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(__('validation.in', ['attribute' => 'delivery type']), $response->json('errors.0.extensions.validation.delivery_type.0'));
        $this->assertEquals(__('validation.in', ['attribute' => 'payment method']), $response->json('errors.0.extensions.validation.payment_method.0'));
        $this->assertEquals(__('validation.exists', ['attribute' => 'address id']), $response->json('errors.0.extensions.validation.address_id.0'));
        $this->assertEquals(__('validation.exists', ['attribute' => 'delivery point id']), $response->json('errors.0.extensions.validation.delivery_point_id.0'));
        $this->assertEquals(__('validation.exists', ['attribute' => 'installment id']), $response->json('errors.0.extensions.validation.installment_id.0'));
        $this->assertEquals(__('validation.exists', ['attribute' => 'cart.0.variation id']), $response->json('errors.0.extensions.validation')['cart.0.variation_id'][0]);
        $this->assertEquals(__('validation.min.numeric', ['attribute' => 'cart.0.quantity', 'min' => 1]), $response->json('errors.0.extensions.validation')['cart.0.quantity'][0]);
    }

    #[Test]
    public function paymentMethodValidations()
    {
        // Payment after delivery is not valid for delivery to address type
        $variables = $this->getInputVariables(variables: [
            'payment_method' => Arr::random(OrderPaymentMethodEnum::payAfterDeliveryMethods()),
            'delivery_type' => OrderDeliveryTypeEnum::DELIVERY_TO_ADDRESS->value,
        ]);
        $response = $this
            ->actingAs($this->_user, 'ecommerce-client')
            ->postJson(route('graphql.ecommerce-client'), $this->buildGraphQLQuery($variables));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals('This payment method is not allowed for the selected delivery type', $response->json('errors.0.extensions.validation.payment_method.0'));

        // Payment after delivery is only valid for delivery point with non-null branch_id
        $variables = $this->getInputVariables(variables: [
            'payment_method' => Arr::random(OrderPaymentMethodEnum::payAfterDeliveryMethods()),
            'delivery_type' => OrderDeliveryTypeEnum::SELF_PICKUP->value,
            'delivery_point_id' => DeliveryPoint::factory()->create(['branch_id' => null])->id,
        ]);
        $response = $this
            ->actingAs($this->_user, 'ecommerce-client')
            ->postJson(route('graphql.ecommerce-client'), $this->buildGraphQLQuery($variables));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals('This payment method is not allowed for the selected delivery type', $response->json('errors.0.extensions.validation.payment_method.0'));
    }

    #[Test]
    public function requiredIfAttributeValidations()
    {
        // Address ID
        $variables = $this->getInputVariables(variables: [
            'delivery_type' => OrderDeliveryTypeEnum::DELIVERY_TO_ADDRESS->value,
            'address_id' => null,
        ]);
        $response = $this
            ->actingAs($this->_user, 'ecommerce-client')
            ->postJson(route('graphql.ecommerce-client'), $this->buildGraphQLQuery($variables));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(
            __('validation.required_if', ['attribute' => 'address id', 'other' => 'delivery type', 'value' => 0]),
            $response->json('errors.0.extensions.validation.address_id.0')
        );

        // Delivery Point ID
        $variables = $this->getInputVariables(variables: [
            'delivery_type' => OrderDeliveryTypeEnum::SELF_PICKUP->value,
            'delivery_point_id' => null,
        ]);
        $response = $this
            ->actingAs($this->_user, 'ecommerce-client')
            ->postJson(route('graphql.ecommerce-client'), $this->buildGraphQLQuery($variables));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(
            __('validation.required_if', ['attribute' => 'delivery point id', 'other' => 'delivery type', 'value' => 1]),
            $response->json('errors.0.extensions.validation.delivery_point_id.0')
        );

        // Installment ID
        $variables = $this->getInputVariables(variables: [
            'payment_method' => OrderPaymentMethodEnum::PAY_BY_INSTALLMENT,
            'installment_id' => null,
        ]);
        $response = $this
            ->actingAs($this->_user, 'ecommerce-client')
            ->postJson(route('graphql.ecommerce-client'), $this->buildGraphQLQuery($variables));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(
            __('validation.required_if', ['attribute' => 'installment id', 'other' => 'payment method', 'value' => 3]),
            $response->json('errors.0.extensions.validation.installment_id.0')
        );
    }

    #[Test]
    public function deliveryServiceUnavailableValidation()
    {
        $variables = $this->getInputVariables(variables: [
            'payment_method' => OrderPaymentMethodEnum::CARD,
            'delivery_type' => OrderDeliveryTypeEnum::DELIVERY_TO_ADDRESS,
        ]);

        $response = $this
            ->actingAs($this->_user, 'ecommerce-client')
            ->postJson(route('graphql.ecommerce-client'), $this->buildGraphQLQuery($variables));

        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals('Delivery service to the specified address is currently unavailable', $response->json('errors.0.extensions.validation.address_id.0'));
    }

    protected function buildGraphQLQuery(array $variables = []): array
    {
        return [
            'query' => <<<GRAPHQL
                mutation CreateOrder(
                    \$delivery_type: Int!
                    \$payment_method: Int!
                    \$address_id: Int
                    \$delivery_point_id: Int
                    \$installment_id: Int
                    \$cart: [UserCartInputGraphQLType!]!
                ) {
                    createOrder(
                        delivery_type: \$delivery_type
                        payment_method: \$payment_method
                        address_id: \$address_id
                        delivery_point_id: \$delivery_point_id
                        installment_id: \$installment_id
                        cart: \$cart
                    ) {
                        {$this->convertIntoQuery($this->_structure)}
                    }
                }
            GRAPHQL,
            'variables' => $variables,
        ];
    }

    private function assertSuccessCreateOrderResponse(TestResponse $response, array $variables, bool $isRequired = false): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure(['data' => ['createOrder' => $this->_structure]]);

        $order = Order::query()->find($response->json('data.createOrder.id'));

        $this->assertEquals($order->delivery_type->value, $response->json('data.createOrder.delivery_type'));
        $this->assertEquals($order->payment_method->value, $response->json('data.createOrder.payment_method'));
        $this->assertEquals($order->shipping_price, $response->json('data.createOrder.shipping_price'));
        $this->assertEquals($order->user_id, $response->json('data.createOrder.user_id'));
        $this->assertEquals($order->address_id, $response->json('data.createOrder.address_id'));
        $this->assertEquals($order->delivery_point_id, $response->json('data.createOrder.delivery_point_id'));
        $this->assertEquals($order->status_id, $response->json('data.createOrder.status_id'));
        $this->assertEquals($order->installment_id, $response->json('data.createOrder.installment_id'));
        $this->assertEquals($order->created_at->timestamp, $response->json('data.createOrder.created_at'));
        $this->assertEquals($order->updated_at->timestamp, $response->json('data.createOrder.updated_at'));
        $this->assertEquals($order->deleted_at?->timestamp, $response->json('data.createOrder.deleted_at'));
    }

    private function getInputVariables(bool $isRequired = false, array $variables = [])
    {
        return array_merge(
            [
                'delivery_type' => (int) OrderDeliveryTypeEnum::randomValue(),
                'payment_method' => (int) OrderPaymentMethodEnum::randomValue(),
                'address_id' => Address::factory()->create()->id,
                'delivery_point_id' => DeliveryPoint::factory()->create()->id,
                'installment_id' => Installment::factory()->create()->id,
                'cart' => [
                    [
                        'variation_id' => ProductVariation::factory()->create(['is_active' => true])->id,
                        'quantity' => 1,
                    ],
                ],
            ],
            $variables
        );
    }
}
