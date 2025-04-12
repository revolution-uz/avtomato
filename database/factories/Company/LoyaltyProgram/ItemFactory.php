<?php

namespace Database\Factories\Company\LoyaltyProgram;

use App\Models\Company\LoyaltyProgram\Item;
use App\Models\Company\LoyaltyProgram\Price;
use App\Models\Company\LoyaltyProgram\Stock;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition(): array
    {
        return [
            'external_code' => $this->faker->unique()->uuid(),
            'external_shop_code' => $this->faker->uuid(),
            'external_art' => $this->faker->optional()->uuid(),
            'external_name' => $this->faker->optional()->word,
            'external_mxik' => $this->faker->optional()->uuid(),
            'external_package_code' => $this->faker->optional()->uuid(),
            'external_gtin' => $this->faker->optional()->uuid(),
        ];
    }

    public function withRelatedData(): static
    {
        return $this->afterCreating(function (Item $item) {
            Price::factory()->createOne(['external_item_code' => $item->external_code, 'external_shop_code' => $item->external_shop_code]);
            Stock::factory()->createOne(['external_item_code' => $item->external_code, 'external_shop_code' => $item->external_shop_code]);
        });
    }
}
