<?php

namespace Database\Factories\Company\Ecommerce;

use App\Models\Company\Ecommerce\ProductOptionGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductOptionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name_uz' => $this->faker->word,
            'name_ru' => $this->faker->word,
            'name_en' => $this->faker->word,
            'name_oz' => $this->faker->word,
            'slug' => $this->faker->unique()->slug,
            'description' => $this->faker->word,

            'group_id' => ProductOptionGroup::factory(),
        ];
    }
}
