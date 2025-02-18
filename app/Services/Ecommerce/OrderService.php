<?php

namespace App\Services\Ecommerce;

use App\Clients\AtmosCardClient;
use App\Enums\Company\OrderDeliveryTypeEnum;
use App\Enums\Company\OrderPaymentMethodEnum;
use App\Enums\Company\PaymentStatusEnum;
use App\Exceptions\GeneralException;
use App\Models\Company\Address;
use App\Models\Company\Ecommerce\DeliveryPoint;
use App\Models\Company\Ecommerce\Order;
use App\Models\Company\Ecommerce\OrderStatus;
use App\Models\Company\Ecommerce\OrderStatusHistory;
use App\Models\Company\Ecommerce\Payment;
use App\Models\Company\Ecommerce\ProductVariation;
use App\Models\Company\Ecommerce\ShippingPrice;
use App\Models\Company\Ecommerce\User;
use App\Models\Company\Ecommerce\UserCart;
use App\Models\Company\TransactionType;
use Illuminate\Support\Carbon;

class OrderService
{
    public function __construct(private AtmosCardClient $_atmosCardClient) {}

    public function createOrder(User $user, array $args): Order
    {
        $order = new Order;
        $order->user_id = $user->id;
        $order->delivery_type = $args['delivery_type'];
        $order->payment_method = $args['payment_method'];

        if ($order->delivery_type === OrderDeliveryTypeEnum::DELIVERY_TO_ADDRESS) {
            $address = Address::query()->find($args['address_id']);
            $order->address_id = $address->id;
            $order->shipping_price = $this->calculateShippingPrice($address);
        } else {
            $deliveryPoint = DeliveryPoint::query()->find($args['delivery_point_id']);
            $order->delivery_point_id = $deliveryPoint->id;
            $order->shipping_price = $deliveryPoint->delivery_fee;
        }

        $order->status_id = ! in_array($order->payment_method, OrderPaymentMethodEnum::payAfterDeliveryMethods())
            ? OrderStatus::WAITING_FOR_PAYMENT
            : OrderStatus::CREATED;

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

    public function payForOrder(Order $order, array $args): Payment
    {
        $createPayResponseDTO = $this->_atmosCardClient->createPayment(
            $order->id,
            $order->full_amount,
            'Payment for order #' . $order->id
        );

        if (! $createPayResponseDTO->result->isSuccess()) {
            throw new GeneralException($createPayResponseDTO->result->description);
        }

        $preConfirmPayResponseDTO = $this->_atmosCardClient->preConfirmPaymentWithOneTimeSms(
            $createPayResponseDTO->transaction_id,
            $args['number'],
            $args['expiry']
        );

        if (! $preConfirmPayResponseDTO->result->isSuccess()) {
            throw new GeneralException($preConfirmPayResponseDTO->result->description);
        }

        return Payment::query()->create([
            'amount' => $createPayResponseDTO->store_transaction->amount / 100,
            'card_pan' => $args['number'],
            'status' => PaymentStatusEnum::WAITING_FOR_CONFIRMATION,
            'external_id' => $createPayResponseDTO->transaction_id,
            'order_id' => $order->id,
            'transaction_type_id' => TransactionType::ATMOS_WITH_CARD,
            'user_id' => $order->user_id,
        ]);
    }

    public function payForOrderConfirm(Payment $payment, string $otp): Payment
    {
        $confirmPayResponseDTO = $this->_atmosCardClient->confirmPayment($payment->external_id, $otp);

        if (! $confirmPayResponseDTO->result->isSuccess()) {
            throw new GeneralException($confirmPayResponseDTO->result->description);
        }

        $payment->status = PaymentStatusEnum::SUCCESS;
        $payment->paid_at = Carbon::createFromTimestampMs($confirmPayResponseDTO->store_transaction->confirm_time);

        if (! $payment->save()) {
            $this->_atmosCardClient->reversePayment($payment->external_id);
        }

        return $payment;
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
