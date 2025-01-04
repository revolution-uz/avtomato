<?php

namespace Database\Factories\Company\Ecommerce;

use App\Models\Company\Address;
use Illuminate\Database\Eloquent\Factories\Factory;

class DeliveryPointFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name_uz' => $this->faker->text(),
            'name_ru' => $this->faker->text(),
            'name_en' => $this->faker->text(),
            'name_oz' => $this->faker->text(),
            'description_uz' => $this->faker->text(),
            'description_ru' => $this->faker->text(),
            'description_en' => $this->faker->text(),
            'description_oz' => $this->faker->text(),
            'is_prepaid' => $this->faker->boolean(),
            'address_id' => Address::factory(),
        ];
    }
}
