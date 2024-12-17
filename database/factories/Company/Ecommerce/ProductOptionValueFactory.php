<?php

namespace Database\Factories\Company\Ecommerce;

use App\Models\Company\Ecommerce\ProductOption;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductOptionValueFactory extends Factory
{
    public function definition(): array
    {
        return [
            'value_uz' => $this->faker->word,
            'value_ru' => $this->faker->word,
            'value_en' => $this->faker->word,
            'value_oz' => $this->faker->word,
            'slug' => $this->faker->unique()->slug,

            'option_id' => ProductOption::factory(),
        ];
    }
}
