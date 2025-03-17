<?php

namespace Database\Factories\Company\LoyaltyProgram;

use App\Models\Company\LoyaltyProgram\Item;
use App\Models\Company\LoyaltyProgram\Order;
use App\Models\Company\LoyaltyProgram\OrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        return [
            'quantity' => $this->faker->numberBetween(1, 10),
            'price' => $this->faker->randomFloat(2, 1, 100),
            'discount_amount' => $this->faker->randomFloat(2, 0, 10),
            'total_amount' => function (array $attributes) {
                return ($attributes['price'] * $attributes['quantity']) - $attributes['discount_amount'];
            },

            'order_id' => Order::factory(),
            'product_id' => Item::factory(),
        ];
    }
}
