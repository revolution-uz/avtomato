<?php

namespace App\Services\Ecommerce;

use App\Models\Company\Ecommerce\Order;
use App\Models\Company\Ecommerce\OrderItem;
use App\Models\Company\Ecommerce\OrderStatus;
use App\Models\Company\Ecommerce\OrderStatusHistory;
use App\Models\Company\Ecommerce\Product;
use App\Models\Company\Ecommerce\User;
use App\Models\Company\Ecommerce\UserCart;

class OrderService
{
    public function createOrder(User $user, $cart, $address_id, $installment_id = null): Order
    {
        $order = new Order;
        $order->user_id = $user->id;
        $order->address_id = $address_id;
        $order->status_id = OrderStatus::CREATED;
        $order->installment_id = $installment_id;
        $order->saveOrFail();
        foreach ($cart as $item) {
            $product = Product::findOrFail($item['product_id']);
            $orderItemAttributes = [
                'order_id' => $order->id,
                'product_id' => $product->id,
                'price' => $product->actual_price,
                'quantity' => $item['quantity'],
            ];
            OrderItem::create($orderItemAttributes);
            UserCart::removeCart($order->user_id, $product->id);
        }

        return $order;
    }

    public function changeOrderStatus(Order $order, int $order_status_id): Order
    {
        $this->createOrderStatusHistory($order, $order_status_id);
        $order->updateOrFail(['status_id' => $order_status_id]);
        $order->refresh();

        return $order;
    }

    private function createOrderStatusHistory(Order $order, int $order_status_id): OrderStatusHistory
    {
        $attributes = [
            'admin_id' => auth()->id(),
            'order_id' => $order->id,
            'from_status_id' => $order->status_id,
            'to_status_id' => $order_status_id,
        ];

        return OrderStatusHistory::create($attributes);
    }
}
