<?php

namespace Database\Factories\Company\Ecommerce;

use App\Enums\Company\OrderDeliveryTypeEnum;
use App\Enums\Company\OrderPaymentMethodEnum;
use App\Models\Company\Address;
use App\Models\Company\Ecommerce\DeliveryPoint;
use App\Models\Company\Ecommerce\OrderStatus;
use App\Models\Company\Ecommerce\User;
use App\Models\Company\Installment;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'delivery_type' => OrderDeliveryTypeEnum::randomValue(),
            'payment_method' => OrderPaymentMethodEnum::randomValue(),
            'shipping_price' => $this->faker->randomFloat(2, 0, pow(10, 8)),
            'user_id' => User::factory(),
            'address_id' => Address::factory(),
            'delivery_point_id' => DeliveryPoint::factory(),
            'status_id' => $this->faker->randomElement([OrderStatus::CREATED, OrderStatus::CANCELLED, OrderStatus::FINISHED]),
            'installment_id' => Installment::factory(),
        ];
    }
}
