<?php

namespace App\Services\LoyaltyProgram;

use App\DTOs\LoyaltyProgram\QrPayloadDTO;
use App\Enums\Company\LoyaltyProgram\OrderStatusEnum;
use App\Http\Resources\OrderResource;
use App\Models\Company\LoyaltyProgram\Item;
use App\Models\Company\LoyaltyProgram\Order;
use App\Models\Company\LoyaltyProgram\OrderItem;
use App\Models\Company\LoyaltyProgram\Price;
use App\Models\Company\LoyaltyProgram\User;
use Exception;
use Illuminate\Support\Facades\DB;

class LoyaltyProgramOrderService
{
    public function createOrder(array $items, string $shop_code, string $qr): OrderResource
    {
        try {
            $qrDto = QrPayloadDTO::fromQr($qr);
            $user = User::query()->with('distributor')->findOrFail($qrDto->user_id);

            if ($qrDto->is_distributed && ! $user->distributor) {
                throw new Exception('User has no distributor');
            }

            if (! $subscription = $qrDto->is_distributed ? $user->distributor->getAvailableSubscription() : $user->getAvailableSubscription()) {
                throw new Exception('User has no active subscription');
            }

            DB::beginTransaction();

            $order = new Order;
            $order->status = OrderStatusEnum::PENDING;
            $order->user_id = $user->id;
            $order->saveOrFail();

            foreach ($items as $item) {
                $this->createItem($item, $shop_code, $order);
            }
            $order->refresh();
            $order->applyOrderDiscount($subscription);

            DB::commit();

            return new OrderResource($order);

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function confirmOrder(int $order_id): false|OrderResource
    {
        $order = Order::query()->findOrFail($order_id);
        $order->confirmOrderDiscount();
        $order->status = OrderStatusEnum::COMPLETED;
        if ($order->save()) {
            return new OrderResource($order);
        }

        return false;
    }

    public function cancelOrder(int $order_id): false|OrderResource
    {
        $order = Order::query()->findOrFail($order_id);
        $order->revertOrderDiscount();
        $order->status = OrderStatusEnum::CANCELED;
        if ($order->save()) {
            return new OrderResource($order);
        }

        return false;
    }

    private function createItem(array $item, string $external_shop_code, Order $order): bool
    {
        $loyaltyProgramItem = Item::query()->firstWhere('external_code', $item['item_code']);
        $loyaltyProgramPrice = Price::query()
            ->where('external_item_code', $item['item_code'])
            ->where('external_shop_code', $external_shop_code)
            ->first();

        if ($loyaltyProgramItem && $loyaltyProgramPrice) {
            $orderItem = new OrderItem;
            $orderItem->order_id = $order->id;
            $orderItem->product_id = $loyaltyProgramItem->id;
            $orderItem->price = $loyaltyProgramPrice->external_price;
            $orderItem->quantity = $item['qty'];
            $orderItem->discount_amount = ($orderItem->price - ($loyaltyProgramPrice->external_discount_price ?? $loyaltyProgramPrice->external_price)) * $orderItem->quantity;

            return $orderItem->save();
        }
        throw new Exception('Item not found');
    }
}
