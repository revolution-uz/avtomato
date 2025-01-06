<?php

namespace Database\Factories\Company\Ecommerce;

use Illuminate\Database\Eloquent\Factories\Factory;

class OrderStatusFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name_uz' => $this->faker->text(),
            'name_ru' => $this->faker->text(),
            'name_en' => $this->faker->text(),
            'name_oz' => $this->faker->text(),
            'is_active' => $this->faker->boolean(),
        ];
    }
}
