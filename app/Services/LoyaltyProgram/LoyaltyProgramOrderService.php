<?php

namespace App\Services\LoyaltyProgram;

use App\Enums\Company\LoyaltyProgramm\OrderStatusEnum;
use App\Models\Company\LoyaltyProgram\Item;
use App\Models\Company\LoyaltyProgram\Order;
use App\Models\Company\LoyaltyProgram\OrderItem;
use App\Models\Company\LoyaltyProgram\User;
use Exception;
use Illuminate\Support\Facades\DB;

class LoyaltyProgramOrderService
{
    public function createOrder(array $items, string $qr): void
    {
        try {
            $user = User::findByQr($qr);

            $subscription = $user->getActiveSubscription();

            if (! $subscription || ! $subscription->is_active_period) {
                throw new Exception('User has no active subscription');
            }

            DB::beginTransaction();

            $order = new Order;
            $order->status = OrderStatusEnum::PENDING;
            $order->user_id = $user->id;
            $order->saveOrFail();

            foreach ($items as $item) {
                $this->createItem($item, $order);
            }

            if ($order->total_discount_amount > $user->active_subscription_limit) {
                throw new Exception('Not enough limit');
            }

            DB::commit();

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function confirmOrder(int $order_id)
    {
        $order = Order::query()->findOrFail($order_id);
        $order->status = OrderStatusEnum::COMPLETED;

        return $order->saveOrFail();
    }

    public function cancelOrder(int $order_id): void
    {
        $order = Order::query()->findOrFail($order_id);
        $order->status = OrderStatusEnum::CANCELED;

        $order->saveOrFail();
    }

    private function createItem(array $item, Order $order)
    {
        if ($loyaltyProgramItem = Item::query()->firstWhere('external_code', $item['item_code'])) {
            $orderItem = new OrderItem;
            $orderItem->order_id = $order->id;
            $orderItem->product_id = $loyaltyProgramItem->id;
            $orderItem->price = $item['price'];
            $orderItem->quantity = $item['qty'];
            $orderItem->discount_amount = $item['discount'];

            return $orderItem->saveOrFail();
        }
        throw new Exception('Item not found');
    }
}
