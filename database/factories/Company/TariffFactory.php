<?php

namespace Database\Factories\Company;

use App\Models\Company\Tariff;
use Illuminate\Database\Eloquent\Factories\Factory;

class TariffFactory extends Factory
{
    protected $model = Tariff::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'is_annual' => $this->faker->boolean(),
            'percent' => $this->faker->randomFloat(),
            'min_amount' => $this->faker->randomNumber(),
            'max_amount' => $this->faker->randomNumber(),
            'min_initial_fee' => $this->faker->randomNumber(),
            'max_initial_fee' => $this->faker->randomNumber(),
            'created_at' => time(),
            'updated_at' => time(),
        ];
    }
}
