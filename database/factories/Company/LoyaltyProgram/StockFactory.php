<?php

namespace Database\Factories\Company\LoyaltyProgram;

use App\Models\Company\LoyaltyProgram\Stock;
use Illuminate\Database\Eloquent\Factories\Factory;

class StockFactory extends Factory
{
    protected $model = Stock::class;

    public function definition(): array
    {
        return [
            'external_item_code' => $this->faker->unique()->uuid(),
            'external_shop_code' => $this->faker->unique()->uuid(),
            'external_quantity' => $this->faker->numberBetween(1, 1000),
        ];
    }
}
