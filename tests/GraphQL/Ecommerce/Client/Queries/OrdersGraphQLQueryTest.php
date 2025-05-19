<?php

namespace Tests\GraphQL\Ecommerce\Client\Queries;

use App\Models\Company\Ecommerce\Order;
use App\Models\Company\Ecommerce\OrderItem;
use App\Models\Company\Ecommerce\OrderStatusHistory;
use App\Models\Company\Ecommerce\User;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class OrdersGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

    private User $_user;

    private int $_totalOrdersCount = 5;

    private array $_structure = [
        'id',
        'user_id',
        'address_id',
        'status_id',
        'installment_id',
        'created_at',
        'updated_at',
        'deleted_at',
        'user' => [
            'id',
            'phone',
            'customer_id',
            'created_at',
            'updated_at',
            'deleted_at',
        ],
        'address' => [
            'id',
            'address',
            'type',
            'region_id',
            'district_id',
            'mahalla_id',
            'created_at',
            'updated_at',
            'deleted_at',
        ],
        'status' => [
            'id',
            'name',
            'name_uz',
            'name_ru',
            'name_en',
            'name_oz',
            'is_active',
            'created_at',
            'updated_at',
            'deleted_at',
        ],
        'status_histories' => [
            '*' => [
                'id',
                'from_status_id',
                'to_status_id',
                'order_id',
                'created_at',
                'updated_at',
                'deleted_at',
            ],
        ],
        'order_items' => [
            '*' => [
                'id',
                'product_id',
                'price',
                'quantity',
                'created_at',
                'updated_at',
                'deleted_at',
            ],
        ],
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->_user = $this->createEcommerceClientUser();

        Order::query()->forceDelete();
        Order::factory()
            ->count($this->_totalOrdersCount)
            ->has(OrderStatusHistory::factory()->count(1), 'statusHistories')
            ->has(OrderItem::factory()->count(1), 'orderItems')
            ->create(['user_id' => $this->_user->id]);
    }

    #[Test]
    public function successOrders(): void
    {
        $response = $this
            ->actingAs($this->_user, 'ecommerce-client')
            ->postJson(route('graphql.ecommerce-client'), $this->buildGraphQLQuery());

        $this->assertSuccessOrdersResponse($response);
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        return [
            'query' => <<<GRAPHQL
                query Orders {
                    orders {
                        {$this->convertIntoQuery($this->_structure)}
                    }
                }
            GRAPHQL,
            'variables' => $variables,
        ];
    }

    private function assertSuccessOrdersResponse(TestResponse $response): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure(['data' => ['orders' => ['*' => $this->_structure]]]);

        $this->assertCount($this->_totalOrdersCount, $response->json('data.orders'));

        $order = $this->_user->orders()->orderBy('created_at', 'desc')->first();

        $this->assertEquals($order->id, $response->json('data.orders.0.id'));
        $this->assertEquals($order->user_id, $response->json('data.orders.0.user_id'));
        $this->assertEquals($order->address_id, $response->json('data.orders.0.address_id'));
        $this->assertEquals($order->status_id, $response->json('data.orders.0.status_id'));
        $this->assertEquals($order->installment_id, $response->json('data.orders.0.installment_id'));
        $this->assertEquals($order->created_at->timestamp, $response->json('data.orders.0.created_at'));
        $this->assertEquals($order->updated_at->timestamp, $response->json('data.orders.0.updated_at'));
        $this->assertEquals($order->deleted_at?->timestamp, $response->json('data.orders.0.deleted_at'));

        // user
        $this->assertEquals($order->user->id, $response->json('data.orders.0.user.id'));
        $this->assertEquals($order->user->phone, $response->json('data.orders.0.user.phone'));
        $this->assertEquals($order->user->customer_id, $response->json('data.orders.0.user.customer_id'));
        $this->assertEquals($order->user->created_at->timestamp, $response->json('data.orders.0.user.created_at'));
        $this->assertEquals($order->user->updated_at->timestamp, $response->json('data.orders.0.user.updated_at'));
        $this->assertEquals($order->user->deleted_at?->timestamp, $response->json('data.orders.0.user.deleted_at'));

        // address
        $this->assertEquals($order->address->id, $response->json('data.orders.0.address.id'));
        $this->assertEquals($order->address->address, $response->json('data.orders.0.address.address'));
        $this->assertEquals($order->address->type->value, $response->json('data.orders.0.address.type'));
        $this->assertEquals($order->address->region_id, $response->json('data.orders.0.address.region_id'));
        $this->assertEquals($order->address->district_id, $response->json('data.orders.0.address.district_id'));
        $this->assertEquals($order->address->mahalla_id, $response->json('data.orders.0.address.mahalla_id'));
        $this->assertEquals($order->address->created_at->timestamp, $response->json('data.orders.0.address.created_at'));
        $this->assertEquals($order->address->updated_at->timestamp, $response->json('data.orders.0.address.updated_at'));
        $this->assertEquals($order->address->deleted_at?->timestamp, $response->json('data.orders.0.address.deleted_at'));

        // status
        $this->assertEquals($order->status->id, $response->json('data.orders.0.status.id'));
        $this->assertEquals($order->status->name, $response->json('data.orders.0.status.name'));
        $this->assertEquals($order->status->name_uz, $response->json('data.orders.0.status.name_uz'));
        $this->assertEquals($order->status->name_ru, $response->json('data.orders.0.status.name_ru'));
        $this->assertEquals($order->status->name_en, $response->json('data.orders.0.status.name_en'));
        $this->assertEquals($order->status->name_oz, $response->json('data.orders.0.status.name_oz'));
        $this->assertEquals($order->status->is_active, $response->json('data.orders.0.status.is_active'));
        $this->assertEquals($order->status->created_at->timestamp, $response->json('data.orders.0.status.created_at'));
        $this->assertEquals($order->status->updated_at->timestamp, $response->json('data.orders.0.status.updated_at'));
        $this->assertEquals($order->status->deleted_at?->timestamp, $response->json('data.orders.0.status.deleted_at'));

        // status_histories
        $this->assertCount($order->statusHistories()->count(), $response->json('data.orders.0.status_histories'));
        $statusHistory = $order->statusHistories()->first();
        $this->assertEquals($statusHistory->id, $response->json('data.orders.0.status_histories.0.id'));
        $this->assertEquals($statusHistory->from_status_id, $response->json('data.orders.0.status_histories.0.from_status_id'));
        $this->assertEquals($statusHistory->to_status_id, $response->json('data.orders.0.status_histories.0.to_status_id'));
        $this->assertEquals($statusHistory->order_id, $response->json('data.orders.0.status_histories.0.order_id'));
        $this->assertEquals($statusHistory->created_at->timestamp, $response->json('data.orders.0.status_histories.0.created_at'));
        $this->assertEquals($statusHistory->updated_at->timestamp, $response->json('data.orders.0.status_histories.0.updated_at'));
        $this->assertEquals($statusHistory->deleted_at?->timestamp, $response->json('data.orders.0.status_histories.0.deleted_at'));

        // order_items
        $this->assertCount($order->orderItems()->count(), $response->json('data.orders.0.order_items'));
        $orderItem = $order->orderItems()->first();
        $this->assertEquals($orderItem->id, $response->json('data.orders.0.order_items.0.id'));
        $this->assertEquals($orderItem->product_id, $response->json('data.orders.0.order_items.0.product_id'));
        $this->assertEquals($orderItem->price, $response->json('data.orders.0.order_items.0.price'));
        $this->assertEquals($orderItem->quantity, $response->json('data.orders.0.order_items.0.quantity'));
        $this->assertEquals($orderItem->created_at->timestamp, $response->json('data.orders.0.order_items.0.created_at'));
        $this->assertEquals($orderItem->updated_at->timestamp, $response->json('data.orders.0.order_items.0.updated_at'));
        $this->assertEquals($orderItem->deleted_at?->timestamp, $response->json('data.orders.0.order_items.0.deleted_at'));
    }
}
