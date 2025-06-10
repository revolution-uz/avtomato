<?php

namespace Unit\Services;

use App\Enums\Company\LoyaltyProgram\OrderStatusEnum;
use App\Models\Company\LoyaltyProgram\Item;
use App\Models\Company\LoyaltyProgram\Order;
use App\Models\Company\LoyaltyProgram\Price;
use App\Models\Company\LoyaltyProgram\Shop;
use App\Models\Company\LoyaltyProgram\Subscription;
use App\Models\Company\LoyaltyProgram\User;
use App\Services\LoyaltyProgram\LoyaltyProgramOrderService;
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
            ->whereNull('shopmate_id')
            ->first();
        $this->assertNotNull($order);
        $this->assertTrue($order->items->count() === count($items));
        $orderItem = $order->items->first();
        $this->assertEquals(1, $orderItem->quantity);
        $this->assertEquals(20_000, $orderItem->price);
        $this->assertEquals(11_000, $orderItem->discount_amount);

        // Check for Subscription
        $this->assertEquals(0, $subscription->refresh()->remaining_limit_amount);
    }

    #[Test]
    public function createOrderIsWorkingWithShopmate()
    {
        $shopmate = User::factory()->create();
        $user = User::factory()->create(['shopmate_id' => $shopmate->id]);
        $subscription = Subscription::factory()->active()->create(['user_id' => $shopmate->id, 'remaining_limit_amount' => 11_000]);
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

        $this->_loyaltyService->createOrder($items, $shop->external_code, $user->getQR(isShopmate: true)['id']);

        // Check for Order
        $order = Order::query()
            ->where('user_id', $user->id)
            ->where('status', OrderStatusEnum::PENDING)
            ->where('shopmate_id', $shopmate->id)
            ->first();
        $this->assertNotNull($order);
        $this->assertTrue($order->items->count() === count($items));
        $orderItem = $order->items->first();
        $this->assertEquals(1, $orderItem->quantity);
        $this->assertEquals(20_000, $orderItem->price);
        $this->assertEquals(11_000, $orderItem->discount_amount);

        // Check for Subscription
        $this->assertEquals(0, $subscription->refresh()->remaining_limit_amount);
    }
}
