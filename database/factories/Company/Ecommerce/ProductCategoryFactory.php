<?php

namespace Database\Factories\Company\Ecommerce;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductCategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'parent_id' => null,
            'name_uz' => $this->faker->word,
            'name_ru' => $this->faker->word,
            'name_en' => $this->faker->word,
            'name_oz' => $this->faker->word,
            'is_active' => $this->faker->boolean,
        ];
    }
}
