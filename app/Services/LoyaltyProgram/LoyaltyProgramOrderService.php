<?php

namespace App\Services\LoyaltyProgram;

use App\Enums\Company\LoyaltyProgramm\OrderStatusEnum;
use App\Models\Company\LoyaltyProgram\Item;
use App\Models\Company\LoyaltyProgram\Order;
use App\Models\Company\LoyaltyProgram\OrderItem;
use App\Models\Company\LoyaltyProgram\Price;
use App\Models\Company\LoyaltyProgram\User;
use Exception;
use Illuminate\Support\Facades\DB;

class LoyaltyProgramOrderService
{
    public function createOrder(array $items, int $shop_id, string $qr): array
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
                $this->createItem($item, $shop_id, $order);
            }

            if ($order->total_discount_amount > $user->active_subscription_limit) {
                throw new Exception('Not enough limit');
            }

            DB::commit();

            return [
                'transaction_id' => $order->id,
                'total_discount_amount' => $order->total_discount_amount,
                'total_amount' => $order->total_amount,
                'items' => $order->items->map(function (OrderItem $item) use ($shop_id) {
                    return [
                        'id' => $item->id,
                        'shop_id' => $shop_id,
                        'item_code' => $item->product->external_code,
                        'price' => $item->price,
                        'qty' => $item->quantity,
                        'discount' => $item->discount_amount,
                        'final_price' => $item->final_price,
                        'total_price' => $item->final_price * $item->quantity,
                    ];
                })->toArray(),
            ];
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

    private function createItem(array $item, int $shop_id, Order $order)
    {
        if (($loyaltyProgramItem = Item::query()->firstWhere('external_code', $item['item_code'])) != null && ($loyaltyProgramPrice = Price::query()->where('external_item_code', $item['item_code'])->where('external_shop_id', $shop_id)->first()) != null) {
            $orderItem = new OrderItem;
            $orderItem->order_id = $order->id;
            $orderItem->product_id = $loyaltyProgramItem->id;
            $orderItem->price = $loyaltyProgramPrice->external_price;
            $orderItem->quantity = $item['qty'];
            $orderItem->discount_amount = $loyaltyProgramPrice->external_price - $item['price'];

            return $orderItem->saveOrFail();
        }
        throw new Exception('Item not found');
    }
}
