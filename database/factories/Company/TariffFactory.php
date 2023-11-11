<?php

namespace Database\Factories\Company;

use App\Models\Company\Tariff;
use Carbon\Carbon;
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
            'min_period' => $this->faker->randomNumber(),
            'max_period' => $this->faker->randomNumber(),
            'min_amount' => $this->faker->randomNumber(),
            'max_amount' => $this->faker->randomNumber(),
            'min_initial_fee' => $this->faker->randomNumber(),
            'max_initial_fee' => $this->faker->randomNumber(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
