<?php

namespace App\Services\Ecommerce;

use App\Enums\Company\OrderDeliveryTypeEnum;
use App\Enums\Company\OrderPaymentMethodEnum;
use App\Models\Company\Address;
use App\Models\Company\Ecommerce\DeliveryPoint;
use App\Models\Company\Ecommerce\Order;
use App\Models\Company\Ecommerce\OrderStatus;
use App\Models\Company\Ecommerce\OrderStatusHistory;
use App\Models\Company\Ecommerce\ProductVariation;
use App\Models\Company\Ecommerce\ShippingPrice;
use App\Models\Company\Ecommerce\User;
use App\Models\Company\Ecommerce\UserCart;

class OrderService
{
    public function createOrder(User $user, array $args): Order
    {
        $order = new Order;
        $order->user_id = $user->id;
        $order->delivery_type = $args['delivery_type'];
        if ($order->delivery_type === OrderDeliveryTypeEnum::DELIVERY_TO_ADDRESS) {
            $address = Address::query()->find($args['address_id']);
            $order->address_id = $address->id;
            $order->shipping_price = $this->calculateShippingPrice($address);
        } else {
            $deliveryPoint = DeliveryPoint::query()->find($args['delivery_point_id']);
            $order->delivery_point_id = $deliveryPoint->id;
            $order->shipping_price = $deliveryPoint->delivery_fee;
        }
        $order->status_id = OrderStatus::CREATED;
        $order->payment_method = $args['payment_method']; // todo: Integrating payment
        $order->installment_id = $order->payment_method === OrderPaymentMethodEnum::PAY_BY_INSTALLMENT ? $args['installment_id'] : null;
        $order->saveOrFail();

        $orderItems = [];
        $variationIds = [];

        foreach ($args['cart'] as $item) {
            $variation = ProductVariation::query()->find($item['variation_id']);
            $orderItems[] = [
                'product_id' => $variation->product_id,
                'variation_id' => $variation->id,
                'price' => $variation->actual_price,
                'quantity' => $item['quantity'],
            ];
            $variationIds[] = $variation->id;
        }

        $order->orderItems()->createMany($orderItems);
        UserCart::clearCart($order->user_id, $variationIds);

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

    private function calculateShippingPrice(Address $address): float
    {
        return ShippingPrice::query()
            ->where('region_id', $address->region_id)
            ->where('district_id', $address->district_id)
            ->where(fn ($q) => $q
                ->where('mahalla_id', $address->mahalla_id)
                ->orWhereNull('mahalla_id'))
            ->first()
            ->price;
    }
}
