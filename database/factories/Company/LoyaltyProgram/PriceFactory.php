<?php

namespace Database\Factories\Company\LoyaltyProgram;

use App\Models\Company\LoyaltyProgram\Price;
use Illuminate\Database\Eloquent\Factories\Factory;

class PriceFactory extends Factory
{
    protected $model = Price::class;

    public function definition()
    {
        return [
            'external_item_code' => $this->faker->unique()->word,
            'external_shop_id' => $this->faker->numberBetween(1, 100),
            'external_price' => $this->faker->randomFloat(2, 1, 1000),
        ];
    }
}
