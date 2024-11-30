<?php

namespace Database\Factories\Company\Ecommerce;

use App\Models\Company\Ecommerce\ProductCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category_id' => ProductCategory::factory(),
            'brand_id' => null,
            'name_uz' => $this->faker->word,
            'name_ru' => $this->faker->word,
            'name_oz' => $this->faker->word,
            'name_en' => $this->faker->word,
            'slug' => $this->faker->slug,
            'cost' => $this->faker->randomFloat(2),
            'actual_price' => $this->faker->randomFloat(2),
            'old_price' => $this->faker->randomFloat(2),
            'is_active' => $this->faker->boolean,
        ];
    }
}
