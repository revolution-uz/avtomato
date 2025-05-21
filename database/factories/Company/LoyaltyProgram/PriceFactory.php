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
            'external_item_code' => $this->faker->unique()->uuid(),
            'external_shop_code' => $this->faker->unique()->uuid(),
            'external_price' => $this->faker->randomFloat(2, 1, pow(10, 8)),
        ];
    }
}
