<?php

namespace Database\Factories\Company\LoyaltyProgram;

use App\Models\Company\LoyaltyProgram\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition(): array
    {
        return [
            'external_code' => $this->faker->unique()->word,
            'external_shop_id' => $this->faker->optional()->numberBetween(1, 100),
            'external_art' => $this->faker->optional()->word,
            'external_name' => $this->faker->optional()->word,
            'external_mxik' => $this->faker->optional()->word,
            'external_package_code' => $this->faker->optional()->word,
            'external_gtin' => $this->faker->optional()->word,
        ];
    }
}
