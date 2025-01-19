<?php

namespace Tests\GraphQL\Ecommerce\Admin\Queries;

use App\Models\Company\Ecommerce\Order;
use App\Models\Company\Ecommerce\OrderItem;
use App\Models\Company\Ecommerce\OrderStatusHistory;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Attributes\Test;
use Tests\GraphQL\GraphQLTestCase;

class SingleOrderGraphQLQueryTest extends GraphQLTestCase
{
    protected bool $tenancy = true;

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
                'admin_id',
                'order_id',
                'created_at',
                'updated_at',
                'deleted_at',
                'from_status' => [
                    'id',
                    'name_uz',
                    'name_ru',
                    'name_en',
                    'name_oz',
                    'is_active',
                    'created_at',
                    'updated_at',
                    'deleted_at',
                ],
                'to_status' => [
                    'id',
                    'name_uz',
                    'name_ru',
                    'name_en',
                    'name_oz',
                    'is_active',
                    'created_at',
                    'updated_at',
                    'deleted_at',
                ],
                'admin' => [
                    'id',
                    'username',
                    'first_name',
                    'last_name',
                    'phone',
                    'status',
                    'language',
                    'role_id',
                    'created_at',
                    'updated_at',
                    'deleted_at',
                ],
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
                'product' => [
                    'id',
                    'name',
                    'name_uz',
                    'name_ru',
                    'name_en',
                    'name_oz',
                    'slug',
                    'cost',
                    'actual_price',
                    'old_price',
                    'is_active',
                    'category_id',
                    'brand_id',
                    'created_at',
                    'updated_at',
                    'deleted_at',
                ],
            ],
        ],
    ];

    #[Test]
    public function successSingleOrder()
    {
        /** @var Order $order */
        $order = Order::factory()->create();
        OrderItem::factory()->count(3)->create(['order_id' => $order->id]);
        OrderStatusHistory::factory()->count(3)->create(['order_id' => $order->id]);

        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['id' => $order->id]));

        $this->assertSuccessSingleOrderResponse($response, $order);
    }

    #[Test]
    public function errorValidation()
    {
        // GraphQL type error
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['id' => null]));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('Variable "$id" of non-null type "Int!" must not be null.', $response->json('errors.0.message'));

        // Rule errors
        $response = $this
            ->actingAs($this->createAdminUser())
            ->postJson(route('graphql.ecommerce-admin'), $this->buildGraphQLQuery(['id' => 999999999]));
        $response->assertOk();
        $this->assertArrayHasKey('errors', $response->json());
        $this->assertEquals('validation', $response->json('errors.0.message'));
        $this->assertEquals(trans('validation.exists', ['attribute' => 'id']), $response->json('errors.0.extensions.validation.id.0'));
    }

    private function buildGraphQLQuery(array $variables = []): array
    {
        return [
            'query' => <<<GRAPHQL
                query SingleOrder(\$id: Int!) {
                    singleOrder(id: \$id) {
                        {$this->convertIntoQuery($this->_structure)}
                    }
                }
            GRAPHQL,
            'variables' => $variables,
        ];
    }

    private function assertSuccessSingleOrderResponse(TestResponse $response, Order $order): void
    {
        $response->assertOk();
        $response->assertJsonIsObject();
        $response->assertJsonStructure(['data' => ['singleOrder' => $this->_structure]]);

        $this->assertEquals($order->id, $response->json('data.singleOrder.id'));
        $this->assertEquals($order->user_id, $response->json('data.singleOrder.user_id'));
        $this->assertEquals($order->address_id, $response->json('data.singleOrder.address_id'));
        $this->assertEquals($order->status_id, $response->json('data.singleOrder.status_id'));
        $this->assertEquals($order->installment_id, $response->json('data.singleOrder.installment_id'));
        $this->assertEquals($order->created_at->timestamp, $response->json('data.singleOrder.created_at'));
        $this->assertEquals($order->updated_at->timestamp, $response->json('data.singleOrder.updated_at'));
        $this->assertEquals($order->deleted_at?->timestamp, $response->json('data.singleOrder.deleted_at'));

        // user
        $this->assertEquals($order->user->id, $response->json('data.singleOrder.user.id'));
        $this->assertEquals($order->user->phone, $response->json('data.singleOrder.user.phone'));
        $this->assertEquals($order->user->customer_id, $response->json('data.singleOrder.user.customer_id'));
        $this->assertEquals($order->user->created_at->timestamp, $response->json('data.singleOrder.user.created_at'));
        $this->assertEquals($order->user->updated_at->timestamp, $response->json('data.singleOrder.user.updated_at'));
        $this->assertEquals($order->user->deleted_at?->timestamp, $response->json('data.singleOrder.user.deleted_at'));

        // address
        $this->assertEquals($order->address->id, $response->json('data.singleOrder.address.id'));
        $this->assertEquals($order->address->address, $response->json('data.singleOrder.address.address'));
        $this->assertEquals($order->address->type->value, $response->json('data.singleOrder.address.type'));
        $this->assertEquals($order->address->region_id, $response->json('data.singleOrder.address.region_id'));
        $this->assertEquals($order->address->district_id, $response->json('data.singleOrder.address.district_id'));
        $this->assertEquals($order->address->mahalla_id, $response->json('data.singleOrder.address.mahalla_id'));
        $this->assertEquals($order->address->created_at->timestamp, $response->json('data.singleOrder.address.created_at'));
        $this->assertEquals($order->address->updated_at->timestamp, $response->json('data.singleOrder.address.updated_at'));
        $this->assertEquals($order->address->deleted_at?->timestamp, $response->json('data.singleOrder.address.deleted_at'));

        // status
        $this->assertEquals($order->status->id, $response->json('data.singleOrder.status.id'));
        $this->assertEquals($order->status->name_uz, $response->json('data.singleOrder.status.name_uz'));
        $this->assertEquals($order->status->name_ru, $response->json('data.singleOrder.status.name_ru'));
        $this->assertEquals($order->status->name_en, $response->json('data.singleOrder.status.name_en'));
        $this->assertEquals($order->status->name_oz, $response->json('data.singleOrder.status.name_oz'));
        $this->assertEquals($order->status->is_active, $response->json('data.singleOrder.status.is_active'));
        $this->assertEquals($order->status->created_at->timestamp, $response->json('data.singleOrder.status.created_at'));
        $this->assertEquals($order->status->updated_at->timestamp, $response->json('data.singleOrder.status.updated_at'));
        $this->assertEquals($order->status->deleted_at?->timestamp, $response->json('data.singleOrder.status.deleted_at'));

        // status_histories
        $this->assertEquals($order->statusHistories()->count(), count($response->json('data.singleOrder.status_histories')));
        $statusHistory = $order->statusHistories->first();
        $this->assertEquals($statusHistory->id, $response->json('data.singleOrder.status_histories.0.id'));
        $this->assertEquals($statusHistory->from_status_id, $response->json('data.singleOrder.status_histories.0.from_status_id'));
        $this->assertEquals($statusHistory->to_status_id, $response->json('data.singleOrder.status_histories.0.to_status_id'));
        $this->assertEquals($statusHistory->admin_id, $response->json('data.singleOrder.status_histories.0.admin_id'));
        $this->assertEquals($statusHistory->order_id, $response->json('data.singleOrder.status_histories.0.order_id'));
        $this->assertEquals($statusHistory->created_at->timestamp, $response->json('data.singleOrder.status_histories.0.created_at'));
        $this->assertEquals($statusHistory->updated_at->timestamp, $response->json('data.singleOrder.status_histories.0.updated_at'));
        $this->assertEquals($statusHistory->deleted_at?->timestamp, $response->json('data.singleOrder.status_histories.0.deleted_at'));

        // status_histories -> from_status
        $this->assertEquals($statusHistory->fromStatus->id, $response->json('data.singleOrder.status_histories.0.from_status.id'));
        $this->assertEquals($statusHistory->fromStatus->name_uz, $response->json('data.singleOrder.status_histories.0.from_status.name_uz'));
        $this->assertEquals($statusHistory->fromStatus->name_ru, $response->json('data.singleOrder.status_histories.0.from_status.name_ru'));
        $this->assertEquals($statusHistory->fromStatus->name_en, $response->json('data.singleOrder.status_histories.0.from_status.name_en'));
        $this->assertEquals($statusHistory->fromStatus->name_oz, $response->json('data.singleOrder.status_histories.0.from_status.name_oz'));
        $this->assertEquals($statusHistory->fromStatus->is_active, $response->json('data.singleOrder.status_histories.0.from_status.is_active'));
        $this->assertEquals($statusHistory->fromStatus->created_at->timestamp, $response->json('data.singleOrder.status_histories.0.from_status.created_at'));
        $this->assertEquals($statusHistory->fromStatus->updated_at->timestamp, $response->json('data.singleOrder.status_histories.0.from_status.updated_at'));
        $this->assertEquals($statusHistory->fromStatus->deleted_at?->timestamp, $response->json('data.singleOrder.status_histories.0.from_status.deleted_at'));

        // status_histories -> to_status
        $this->assertEquals($statusHistory->toStatus->id, $response->json('data.singleOrder.status_histories.0.to_status.id'));
        $this->assertEquals($statusHistory->toStatus->name_uz, $response->json('data.singleOrder.status_histories.0.to_status.name_uz'));
        $this->assertEquals($statusHistory->toStatus->name_ru, $response->json('data.singleOrder.status_histories.0.to_status.name_ru'));
        $this->assertEquals($statusHistory->toStatus->name_en, $response->json('data.singleOrder.status_histories.0.to_status.name_en'));
        $this->assertEquals($statusHistory->toStatus->name_oz, $response->json('data.singleOrder.status_histories.0.to_status.name_oz'));
        $this->assertEquals($statusHistory->toStatus->is_active, $response->json('data.singleOrder.status_histories.0.to_status.is_active'));
        $this->assertEquals($statusHistory->toStatus->created_at->timestamp, $response->json('data.singleOrder.status_histories.0.to_status.created_at'));
        $this->assertEquals($statusHistory->toStatus->updated_at->timestamp, $response->json('data.singleOrder.status_histories.0.to_status.updated_at'));
        $this->assertEquals($statusHistory->toStatus->deleted_at?->timestamp, $response->json('data.singleOrder.status_histories.0.to_status.deleted_at'));

        // status_histories -> admin
        $this->assertEquals($statusHistory->admin->id, $response->json('data.singleOrder.status_histories.0.admin.id'));
        $this->assertEquals($statusHistory->admin->username, $response->json('data.singleOrder.status_histories.0.admin.username'));
        $this->assertEquals($statusHistory->admin->first_name, $response->json('data.singleOrder.status_histories.0.admin.first_name'));
        $this->assertEquals($statusHistory->admin->last_name, $response->json('data.singleOrder.status_histories.0.admin.last_name'));
        $this->assertEquals($statusHistory->admin->phone, $response->json('data.singleOrder.status_histories.0.admin.phone'));
        $this->assertEquals($statusHistory->admin->status->value, $response->json('data.singleOrder.status_histories.0.admin.status'));
        $this->assertEquals($statusHistory->admin->language, $response->json('data.singleOrder.status_histories.0.admin.language'));
        $this->assertEquals($statusHistory->admin->role_id, $response->json('data.singleOrder.status_histories.0.admin.role_id'));
        $this->assertEquals($statusHistory->admin->created_at->timestamp, $response->json('data.singleOrder.status_histories.0.admin.created_at'));
        $this->assertEquals($statusHistory->admin->updated_at->timestamp, $response->json('data.singleOrder.status_histories.0.admin.updated_at'));
        $this->assertEquals($statusHistory->admin->deleted_at?->timestamp, $response->json('data.singleOrder.status_histories.0.admin.deleted_at'));

        // order_items
        $this->assertEquals($order->orderItems()->count(), count($response->json('data.singleOrder.order_items')));
        $orderItem = $order->orderItems->first();
        $this->assertEquals($orderItem->id, $response->json('data.singleOrder.order_items.0.id'));
        $this->assertEquals($orderItem->product_id, $response->json('data.singleOrder.order_items.0.product_id'));
        $this->assertEquals($orderItem->price, $response->json('data.singleOrder.order_items.0.price'));
        $this->assertEquals($orderItem->quantity, $response->json('data.singleOrder.order_items.0.quantity'));
        $this->assertEquals($orderItem->created_at->timestamp, $response->json('data.singleOrder.order_items.0.created_at'));
        $this->assertEquals($orderItem->updated_at->timestamp, $response->json('data.singleOrder.order_items.0.updated_at'));
        $this->assertEquals($orderItem->deleted_at?->timestamp, $response->json('data.singleOrder.order_items.0.deleted_at'));

        // order_items -> product
        $this->assertEquals($orderItem->product->id, $response->json('data.singleOrder.order_items.0.product.id'));
        $this->assertEquals($orderItem->product->name, $response->json('data.singleOrder.order_items.0.product.name'));
        $this->assertEquals($orderItem->product->name_uz, $response->json('data.singleOrder.order_items.0.product.name_uz'));
        $this->assertEquals($orderItem->product->name_ru, $response->json('data.singleOrder.order_items.0.product.name_ru'));
        $this->assertEquals($orderItem->product->name_en, $response->json('data.singleOrder.order_items.0.product.name_en'));
        $this->assertEquals($orderItem->product->name_oz, $response->json('data.singleOrder.order_items.0.product.name_oz'));
        $this->assertEquals($orderItem->product->slug, $response->json('data.singleOrder.order_items.0.product.slug'));
        $this->assertEquals($orderItem->product->cost, $response->json('data.singleOrder.order_items.0.product.cost'));
        $this->assertEquals($orderItem->product->actual_price, $response->json('data.singleOrder.order_items.0.product.actual_price'));
        $this->assertEquals($orderItem->product->old_price, $response->json('data.singleOrder.order_items.0.product.old_price'));
        $this->assertEquals($orderItem->product->is_active, $response->json('data.singleOrder.order_items.0.product.is_active'));
        $this->assertEquals($orderItem->product->category_id, $response->json('data.singleOrder.order_items.0.product.category_id'));
        $this->assertEquals($orderItem->product->brand_id, $response->json('data.singleOrder.order_items.0.product.brand_id'));
        $this->assertEquals($orderItem->product->created_at->timestamp, $response->json('data.singleOrder.order_items.0.product.created_at'));
        $this->assertEquals($orderItem->product->updated_at->timestamp, $response->json('data.singleOrder.order_items.0.product.updated_at'));
        $this->assertEquals($orderItem->product->deleted_at?->timestamp, $response->json('data.singleOrder.order_items.0.product.deleted_at'));
    }
}
