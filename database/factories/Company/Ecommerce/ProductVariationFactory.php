<?php

namespace Database\Factories\Company\Ecommerce;

use App\Models\Company\Ecommerce\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductVariationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'is_active' => $this->faker->boolean(),
            'cost' => $this->faker->randomFloat(2, 1, 100),
            'actual_price' => $this->faker->randomFloat(2, 1, 100),
            'old_price' => $this->faker->randomFloat(2, 1, 100),

            'product_id' => Product::factory(),
        ];
    }
}
