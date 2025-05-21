<?php

namespace Database\Factories\Company\Ecommerce;

use App\Models\Company\Ecommerce\Order;
use App\Models\Company\Ecommerce\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'order_id' => Order::factory(),
            'price' => $this->faker->randomFloat(2),
            'quantity' => $this->faker->numberBetween(1, 10),
        ];
    }
}
