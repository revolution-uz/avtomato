<?php

namespace Unit\Services;

use App\Enums\Company\LoyaltyProgram\OrderStatusEnum;
use App\Models\Company\LoyaltyProgram\Item;
use App\Models\Company\LoyaltyProgram\Order;
use App\Models\Company\LoyaltyProgram\OrderItem;
use App\Models\Company\LoyaltyProgram\Price;
use App\Models\Company\LoyaltyProgram\Shop;
use App\Models\Company\LoyaltyProgram\Subscription;
use App\Models\Company\LoyaltyProgram\User;
use App\Services\LoyaltyProgram\LoyaltyProgramOrderService;
use Exception;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LoyaltyProgramOrderServiceTest extends TestCase
{
    protected bool $tenancy = true;

    private LoyaltyProgramOrderService $_loyaltyService;

    protected function setUp(): void
    {
        parent::setUp();

        Order::query()->truncate();
        User::query()->truncate();
        Subscription::query()->truncate();
        Shop::query()->truncate();
        Item::query()->truncate();
        Price::query()->truncate();

        $this->_loyaltyService = app(LoyaltyProgramOrderService::class);
    }

    #[Test]
    public function createOrderIsWorking()
    {
        $user = User::factory()->create();
        $subscription = Subscription::factory()->active()->create(['user_id' => $user->id, 'remaining_limit_amount' => 11_000]);
        $shop = Shop::factory()->create();
        $item = Item::factory()->create(['external_shop_code' => $shop->external_code]);
        Price::factory()->create([
            'external_price' => 20_000,
            'external_discount_price' => 9_000,
            'external_item_code' => $item->external_code,
            'external_shop_code' => $shop->external_code,
        ]);

        $items = [
            [
                'item_code' => $item->external_code,
                'qty' => 1,
            ],
        ];

        $this->_loyaltyService->createOrder($items, $shop->external_code, $user->getQR()['id']);

        // Check for Order
        $order = Order::query()
            ->where('user_id', $user->id)
            ->where('status', OrderStatusEnum::PENDING)
            ->where('subscription_id', $subscription->id)
            ->first();
        $this->assertNotNull($order);
        $this->assertTrue($order->items->count() === count($items));
        $orderItem = $order->items->first();
        $this->assertEquals(1, $orderItem->quantity);
        $this->assertEquals(20_000, $orderItem->price);
        $this->assertEquals(11_000, $orderItem->discount_amount);

        // Check for Subscription
        $this->assertEquals(11_000, $subscription->refresh()->remaining_limit_amount);
    }

    #[Test]
    public function createOrderIsWorkingWithDistributor()
    {
        $distributor = User::factory()->create();
        $user = User::factory()->create(['distributor_id' => $distributor->id]);
        $subscription = Subscription::factory()->active()->create(['user_id' => $distributor->id, 'remaining_limit_amount' => 11_000]);
        $shop = Shop::factory()->create();
        $item = Item::factory()->create(['external_shop_code' => $shop->external_code]);
        Price::factory()->create([
            'external_price' => 20_000,
            'external_discount_price' => 9_000,
            'external_item_code' => $item->external_code,
            'external_shop_code' => $shop->external_code,
        ]);

        $items = [
            [
                'item_code' => $item->external_code,
                'qty' => 1,
            ],
        ];

        $this->_loyaltyService->createOrder($items, $shop->external_code, $user->getQR(isDistributed: true)['id']);

        // Check for Order
        $order = Order::query()
            ->where('user_id', $user->id)
            ->where('status', OrderStatusEnum::PENDING)
            ->where('subscription_id', $subscription->id)
            ->first();
        $this->assertNotNull($order);
        $this->assertTrue($order->items->count() === count($items));
        $orderItem = $order->items->first();
        $this->assertEquals(1, $orderItem->quantity);
        $this->assertEquals(20_000, $orderItem->price);
        $this->assertEquals(11_000, $orderItem->discount_amount);

        // Check for Subscription
        $this->assertEquals(11_000, $subscription->refresh()->remaining_limit_amount);
    }

    #[Test]
    public function failCreateOrderWhenSubscriptionLimitIsNotEnough()
    {
        $user = User::factory()->create();
        Subscription::factory()->active()->create(['user_id' => $user->id, 'remaining_limit_amount' => 11_000]);
        $shop = Shop::factory()->create();
        $item = Item::factory()->create(['external_shop_code' => $shop->external_code]);
        Price::factory()->create([
            'external_price' => 20_000,
            'external_discount_price' => 8_000,
            'external_item_code' => $item->external_code,
            'external_shop_code' => $shop->external_code,
        ]);

        $items = [
            [
                'item_code' => $item->external_code,
                'qty' => 1,
            ],
        ];

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Not enough limit on subscription');

        $this->_loyaltyService->createOrder($items, $shop->external_code, $user->getQR()['id']);
    }

    #[Test]
    public function confirmOrderIsWorking()
    {
        $subscription = Subscription::factory()->active()->create(['remaining_limit_amount' => 11_000]);
        $order = Order::factory()->create(['status' => OrderStatusEnum::PENDING, 'subscription_id' => $subscription->id]);
        OrderItem::factory()->create(['discount_amount' => 11_000, 'order_id' => $order->id]);

        $this->_loyaltyService->confirmOrder($order->id);

        // Check for Order
        $this->assertEquals(OrderStatusEnum::COMPLETED, $order->refresh()->status);

        // Check for Subscription
        $this->assertEquals(0, $subscription->refresh()->remaining_limit_amount);
    }

    #[Test]
    public function failConfirmOrderWhenSubscriptionIsNotEnough()
    {
        $subscription = Subscription::factory()->active()->create(['remaining_limit_amount' => 11_000]);
        $order = Order::factory()->create(['status' => OrderStatusEnum::PENDING, 'subscription_id' => $subscription->id]);
        OrderItem::factory()->create(['discount_amount' => 12_000, 'order_id' => $order->id]);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Not enough limit on subscription');

        $this->_loyaltyService->confirmOrder($order->id);
    }

    #[Test]
    public function cancelOrderIsWorking()
    {
        $subscription = Subscription::factory()->active()->create(['remaining_limit_amount' => 0]);
        $order = Order::factory()->create(['status' => OrderStatusEnum::PENDING, 'subscription_id' => $subscription->id]);
        OrderItem::factory()->create(['discount_amount' => 11_000, 'order_id' => $order->id]);

        $this->_loyaltyService->cancelOrder($order->id);

        // Check for Order
        $this->assertEquals(OrderStatusEnum::CANCELED, $order->refresh()->status);

        // Check for Subscription
        $this->assertEquals(11_000, $subscription->refresh()->remaining_limit_amount);
    }
}
