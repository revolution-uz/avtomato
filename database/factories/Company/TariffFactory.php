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
            'percent' => $this->faker->randomFloat(),
            'min_period' => $this->faker->randomNumber(1, true),
            'max_period' => $this->faker->randomNumber(2, true),
            'min_amount' => $this->faker->randomNumber(3, true),
            'max_amount' => $this->faker->randomNumber(6, true),
            'min_initial_fee' => $this->faker->randomNumber(3, true),
            'max_initial_fee' => $this->faker->randomNumber(6, true),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
